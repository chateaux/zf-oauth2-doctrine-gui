<?php
namespace DoctrineGui\Controller;

use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\JwtService;
use DoctrineGui\Service\ScopeService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\FormInterface;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\OAuth2\Doctrine\Adapter\DoctrineAdapter;
use ZF\OAuth2\Doctrine\Entity\Client;
use ZF\OAuth2\Doctrine\Entity\Jwt;
use ZF\OAuth2\Client\Service\Jwt as JwtClient;
use ZfcRbac\Service\AuthorizationService;

class DoctrineGuiController extends AbstractActionController
{

    /**
     * @param ClientService $clientService
     * @param JwtService $jwtService
     * @param ScopeService $scopeService
     * @param AccessTokenService $accessTokenService
     * @param DoctrineAdapter $doctrineAdapter
     * @param FormInterface $clientForm
     * @param FormInterface $jwtForm
     * @param FormInterface $generateJwtForm
     * @param ApplicationSettingsInterface $applicationSettings
     */
    public function __construct(
        ClientService          $clientService,
        JwtService             $jwtService,
        ScopeService           $scopeService,
        AccessTokenService     $accessTokenService,
        DoctrineAdapter        $doctrineAdapter,
        FormInterface          $clientForm,
        FormInterface          $jwtForm,
        FormInterface          $generateJwtForm,
        ApplicationSettingsInterface $applicationSettings

    ) {
        $this->clientService   = $clientService;
        $this->jwtService      = $jwtService;
        $this->scopeService    = $scopeService;
        $this->accessTokenService = $accessTokenService;
        $this->doctrinAdapter  = $doctrineAdapter;
        $this->clientForm      = $clientForm;
        $this->jwtForm         = $jwtForm;
        $this->testJwtForm = $generateJwtForm;
        $this->applicationSettings = $applicationSettings;
    }

    /**
     * View the clients
     * @return Response
     */
    public function overviewAction()
    {


    }

    /**
     * List the clients
     * @return Response|ViewModel
     */
    public function clientsAction()
    {

        $client_object_array = $this->clientService->find();

        $client_array = [];

        foreach ($client_object_array AS $clientObject)
        {
            $pub_key = [];
            $array_copy = [];
            $client_scopes = '';
            if ($clientObject instanceof Client)
            {
                $jwtObject = $this->jwtService->findByClientId($clientObject);

                if ($jwtObject instanceof Jwt )
                {
                    $pub_key = [
                        'id' => $jwtObject->getId(),
                        'client_id' => $clientObject->getId(),
                        'subject' => $jwtObject->getSubject(),
                        'key' => ($jwtObject->getPublicKey() != '') ? $jwtObject->getPublicKey() : 'The key is blank, please add a server key'
                    ];
                }

                $array_copy = $clientObject->getArrayCopy();

                $client_scopes = $clientObject->getScope();

            }

            $client_array[] = [
                'array_copy' => $array_copy,
                'public_key' => $pub_key,
                'scope' => $client_scopes
            ];

        }

        $app_url = $this->applicationSettings->getSettings('app_url');

        return new ViewModel(
            [
                'client_array' => $client_array,
                'app_url' => $app_url
            ]
        );

    }

    /**
     * Edit and add new clients
     * @return array|Response|ViewModel
     */
    public function clientManageAction()
    {

        $client_id = (int) $this->params()->fromRoute('client_id', 0);

        $prg = $this->prg();

        if ( $prg instanceof Response ) {
            return $prg;
        } elseif ($prg === false) {

            if ($client_id != 0)
            {
                $clientObject = $this->clientService->find($client_id);
                $this->clientForm->bind($clientObject);
            }

            return new ViewModel( array( 'form' => $this->clientForm , 'client_id' => $client_id) );

        }

        /**
         * Remove the scopes from PRG as this crashes the programme
         */
        $scope_array = $prg['client']['scope'];
        unset($prg['client']['scope']);

        $this->clientForm->setData($prg);

        if ( ! $this->clientForm->isValid() ) {

            return new ViewModel( array( 'form' => $this->clientForm , 'client_id' => $client_id ) );
        }

        /**
         * Check the passwords
         */
        $new_password = '';

        if (isset($prg['client']['password']) AND isset($prg['client']['passwordRepeat']))
        {
            if ($prg['client']['password'] != $prg['client']['passwordRepeat']) {
                $this->flashMessenger()->addErrorMessage(
                    'Passwords do not match'
                );

                return $this->redirect()->toRoute('developer/client-manage' , ['client_id' => $client_id]);
            } else {
                $new_password = $prg['client']['password'];
            }
        }

        /**
         * Check for pre-existing client
         */
        if ($client_id == 0)
        {
            $testClient = $this->clientService->findByClientId($prg['client']['clientId']);

            if ($testClient instanceof Client)
            {
                $this->flashMessenger()->addErrorMessage(
                    'The client '.$prg['client']['clientId'].' exists, please choose another client name'
                );

                return $this->redirect()->toRoute('developer/client-manage' , ['client_id' => $client_id]);
            }

            /**
             * Make sure the passwords are not blank
             */
            if ($prg['client']['password'] == '')
            {
                $this->flashMessenger()->addErrorMessage(
                    'Please set the password'
                );

                return $this->redirect()->toRoute('developer/client-manage' , ['client_id' => $client_id]);
            }

        }

        $clientObject = $this->clientForm->getData();

        /**
         * Set the scopes
         */
        //Step 1 remove all associated scopes
        if ($client_id != 0)
        {
            $this->scopeService->removeScopes($clientObject);
        }

        foreach ($scope_array as $scope) {
            $scopeObject = $this->scopeService->find($scope);
            $scopeObject->addClient($clientObject);
            $clientObject->addScope($scopeObject);
        }

        $clientObject->setUser();  //@TODO Add user

        if ($new_password != '')
        {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            $clientObject->setSecret($bcrypt->create($new_password));
        }

        $clientObject = $this->clientService->update($clientObject);

        if ( ! $clientObject instanceof Client )
        {
            $this->flashMessenger()->addErrorMessage(
                'Error updating the client'
            );

            return $this->redirect()->toRoute('developer/client-manage' , ['client_id' => $client_id]);
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client updated'
        );

        return $this->redirect()->toRoute('developer/clients');
    }

    /**
     * Edit a Jwt key
     * @return array|Response|ViewModel
     */
    public function manageKeyAction()
    {
        $jwt_id = (int) $this->params()->fromRoute('jwt_id', false);
        $client_id = (int) $this->params()->fromRoute('client_id', 0);

        $jwt = $this->jwtService->findByClientId($client_id);

        if ($jwt instanceof Jwt AND $jwt_id == 0)
        {
            $this->flashMessenger()->addErrorMessage(
                'You have a pre-existing public key for this client, either delete the key then add a new one or edit the current key.'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        $prg = $this->prg();

        if ( $prg instanceof Response ) {
            return $prg;
        } elseif ($prg === false) {

            if ($jwt_id != 0)
            {
                $jwtObject = $this->jwtService->find($jwt_id);
                $this->jwtForm->bind($jwtObject);
            } else {
                $jwtObject = new Jwt();
                $clientObject = $this->clientService->find($client_id);
                $jwtObject->setClient($clientObject);
                $jwtObject->setSubject();    //@TODO Add the client id
                $this->jwtForm->bind($jwtObject);
            }

            return new ViewModel( array( 'form' => $this->jwtForm , 'jwt_id' => $jwt_id) );

        }

        $this->jwtForm->setData($prg);

        if ( ! $this->jwtForm->isValid() ) {

            return new ViewModel( array( 'form' => $this->jwtForm , 'jwt_id' => $jwt_id ) );
        }

        $jwtObject = $this->jwtForm->getData();

        $jwtObject = $this->jwtService->update($jwtObject);

        if ( ! $jwtObject instanceof Jwt)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to save the jwt object'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client updated'
        );

        return $this->redirect()->toRoute('developer/clients');

    }

    /**
     * Remove unwanted key
     * @return Response
     */
    public function deleteJwtKeyAction()
    {
        $jwt_id = (int) $this->params()->fromRoute('jwt_id', false);


        if ( ! $jwt_id )
        {
            $this->flashMessenger()->addErrorMessage(
                'Missing key'
            );

            return $this->redirect()->toRoute('developer/clients');
        }



        $result = $this->jwtService->delete($jwt_id);

        if (!$result)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to delete Key'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Key deleted'
        );

        return $this->redirect()->toRoute('developer/clients');


    }

    /**
     * Remove a client
     * @return Response
     */
    public function deleteClientAction()
    {
        $client_id = (int) $this->params()->fromRoute('client_id', false);



        if ( ! $client_id )
        {
            $this->flashMessenger()->addErrorMessage(
                'Invalid client'
            );

            return $this->redirect()->toRoute('clients');
        }

        /**
         * Check if a client has keys
         */
        $jwtObject = $this->jwtService->findByClientId($client_id);

        if (!empty($jwtObject))
        {
            $this->flashMessenger()->addErrorMessage(
                'Remove the client keys before deleting the client'
            );

            return $this->redirect()->toRoute('developer/clients');
        }


        $result = $this->clientService->delete($client_id);

        if (!$result)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to delete client'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client deleted'
        );

        return $this->redirect()->toRoute('developer/clients');


    }

    /**
     * Creates a test JWT token to be tested in an api client such as PostMan
     * @return ViewModel
     */
    public function testJwtAction()
    {

        $jwt_id = (int) $this->params()->fromRoute('jwt_id', false);
        $client_id = (int) $this->params()->fromRoute('client_id', false);

        $jwtObject = $this->jwtService->find($jwt_id);
        $clientObject = $this->clientService->find($client_id);

        if ( ! $this->authService->hasIdentity() ) {
            return $this->redirect()->toRoute('customer/login');
        }

        if ( ! $clientObject instanceof Client )
        {
            $this->flashMessenger()->addErrorMessage(
                'Missing client object'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        if ( ! $jwtObject instanceof Jwt )
        {
            $this->flashMessenger()->addErrorMessage(
                'Missing jwt object'
            );

            return $this->redirect()->toRoute('developer/clients');
        }

        $jwt_array = [
            'issuer' => $clientObject->getClientId(),
            'subject'   => $jwtObject->getSubject()
        ];

        $prg = $this->prg();

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return new ViewModel(
                [
                    'form' => $this->testJwtForm,
                    'client_id' => $client_id,
                    'jwt_id' => $jwt_id ,
                    'jwt_array' => $jwt_array,
                    'jwt' => ''
                ]
            );
        }

        $privateKey = $prg['privkey'];

        $iss = $prg['iss'];
        $sub = $prg['sub'];
        $aud = $prg['aud'];
        $exp = $prg['exp'];
        $nbf = $prg['nbt'];
        $jti = $prg['jti'];

        $jwtService = new JwtClient();

        $jwt = $jwtService->generate($privateKey, $iss, $sub, $aud, $exp, $nbf, $jti);

        return new ViewModel(
            [
                'form' => $this->testJwtForm,
                'client_id' => $client_id,
                'jwt_id' => $jwt_id ,
                'jwt_array' => $jwt_array,
                'jwt' => $jwt
            ]
        );


    }


}