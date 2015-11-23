<?php
namespace DoctrineGui\Controller;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

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
use ZF\OAuth2\Doctrine\Entity\Scope;

class DoctrineGuiController extends AbstractActionController
{

    /**
     * @param ClientService $clientService
     * @param JwtService $jwtService
     * @param ScopeService $scopeService
     * @param AccessTokenService $accessTokenService
     * @param FormInterface $clientForm
     * @param FormInterface $jwtForm
     * @param FormInterface $generateJwtForm
     * @param FormInterface $scopeForm
     */
    public function __construct(
        ClientService          $clientService,
        JwtService             $jwtService,
        ScopeService           $scopeService,
        AccessTokenService     $accessTokenService,
        FormInterface          $clientForm,
        FormInterface          $jwtForm,
        FormInterface          $generateJwtForm,
        FormInterface          $scopeForm

    ) {
        $this->clientService   = $clientService;
        $this->jwtService      = $jwtService;
        $this->scopeService    = $scopeService;
        $this->accessTokenService = $accessTokenService;
        $this->clientForm      = $clientForm;
        $this->jwtForm         = $jwtForm;
        $this->testJwtForm     = $generateJwtForm;
        $this->scopeForm       = $scopeForm;
    }

    /**
     * View the clients
     * @return Response
     */
    public function overviewAction()
    {

        return new ViewModel([]);
    }

    /**
     * List the clients
     * @return Response|ViewModel
     */
    public function clientsAction()
    {

        $client_object_array = $this->clientService->findAll();

        $client_array = [];

        foreach ($client_object_array AS $clientObject)
        {
            $pub_key = [];
            $array_copy = [];
            $client_scopes = [];

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

                $persistentCollection = $clientObject->getScope();
                $scopes = $persistentCollection->getValues();

                foreach ($scopes AS $scopeObject)
                {
                    $client_scopes[] = $scopeObject->getScope();
                }

            }

            $client_array[] = [
                'array_copy' => $array_copy,
                'public_key' => $pub_key,
                'scope' => $client_scopes
            ];

        }

        return new ViewModel(
            [
                'client_array' => $client_array
            ]
        );

    }

    /**
     * @return ViewModel
     */
    public function scopesAction()
    {

        $scope_id = $this->params()->fromRoute('scope_id',0);

        $scopes = $this->scopeService->findAll();
        $scopes_array = [];

        foreach ($scopes AS $scopeObject)
        {
            $scopes_array[] = $scopeObject->getArrayCopy();
        }

        $prg = $this->prg();

        if ( $prg instanceof Response ) {
            return $prg;
        } elseif ($prg === false) {

            if ($scope_id != 0)
            {
                $scopeObject = $this->scopeService->find($scope_id);

            } else {
                $scopeObject = new Scope();
            }

            $this->scopeForm->bind($scopeObject);

            return new ViewModel(
                array(
                    'scopes_array' => $scopes_array,
                    'form' => $this->scopeForm
                )
            );

        }

        $this->scopeForm->setData($prg);

        if ( ! $this->scopeForm->isValid() ) {

            return new ViewModel(
                array(
                    'scopes_array' => $scopes_array,
                    'form' => $this->scopeForm
                )
            );
        }

        /**
         * Test for pre-existing scope
         */
        if ($scope_id == 0)
        {
            $testScope = $this->scopeService->findByName($prg['scope']['scope']);

            if ($testScope instanceof Scope)
            {
                $this->flashMessenger()->addErrorMessage(
                    'The scope "'.$prg['scope']['scope'].'" exists, please choose another scope name'
                );

                return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');
            }

        }

        $scopeObject = $this->scopeForm->getData();

        $this->scopeService->update($scopeObject);

        $message = ($scope_id == 0) ? 'The scope "'.$prg['scope']['scope'].'" has been added' : 'The scope "'.$prg['scope']['scope'].'" has been updated';

        $this->flashMessenger()->addSuccessMessage($message);

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');



    }

    /**
     * Removes scopes
     * @return Response
     */
    public function scopeDeleteAction()
    {
        $scope_id = $this->params()->fromRoute('scope_id',false);

        if (!$scope_id)
        {
            $this->flashMessenger()->addErrorMessage(
                'Invalid scope identity'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');
        }

        $this->scopeService->delete($scope_id);

        $this->flashMessenger()->addSuccessMessage(
            'Scope deleted'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');

    }

    /**
     * Toggle default settings
     * @return Response
     */
    public function scopeToggleAction()
    {
        $scope_id = $this->params()->fromRoute('scope_id',false);

        if (!$scope_id)
        {
            $this->flashMessenger()->addErrorMessage(
                'Invalid scope identity'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');
        }

        $this->scopeService->toggle($scope_id);

        $this->flashMessenger()->addSuccessMessage(
            'Scope default toggled'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/scopes');

    }

    /**
     * Edit and add new clients
     * client_id refers to the id field of the client (1,2,3...n)
     * @return array|Response|ViewModel
     */
    public function clientManageAction()
    {

        $client_id = (int) $this->params()->fromRoute('client_id', 0);

        $prg = $this->prg();

        if ( $prg instanceof Response ) {
            return $prg;
        } elseif ($prg === false) {

            $user_id = '';

            if ($client_id != 0)
            {
                $clientObject = $this->clientService->find($client_id);
                $this->clientForm->bind($clientObject);
                $userObject = $clientObject->getUser();

                if (is_object($userObject)) {
                    $user_id = $userObject->getId();
                }

            }

            return new ViewModel(
                array(
                    'form' => $this->clientForm,
                    'client_id' => $client_id,
                    'user_id' => $user_id
                )
            );

        }

        /**
         * Remove the scopes from PRG as this crashes the programme
         */
        $scope_array = $prg['client']['scope'];
        unset($prg['client']['scope']);

        $this->clientForm->setData($prg);

        if ( ! $this->clientForm->isValid() ) {

            return new ViewModel(
                array(
                    'form' => $this->clientForm,
                    'client_id' => $client_id,
                    'user_id' => $prg['client']['user']
                )
            );
        }

        /**
         * Check the passwords
         */
        $newPassword = '';

        if (isset($prg['client']['password']) AND isset($prg['client']['passwordRepeat']))
        {
            if ($prg['client']['password'] != $prg['client']['passwordRepeat']) {
                $this->flashMessenger()->addErrorMessage(
                    'Passwords do not match'
                );

                return $this->redirect()->toRoute('zf-oauth-doctrine-gui/client-manage' , ['client_id' => $client_id]);

            } else {
                $newPassword = $prg['client']['password'];
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

                return $this->redirect()->toRoute('zf-oauth-doctrine-gui/client-manage' , ['client_id' => $client_id]);
            }

            /**
             * Make sure the passwords are not blank
             */
            if ($prg['client']['password'] == '')
            {
                $this->flashMessenger()->addErrorMessage(
                    'Please set the password'
                );

                return $this->redirect()->toRoute('zf-oauth-doctrine-gui/client-manage' , ['client_id' => $client_id]);
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

        if ($newPassword)
        {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            $clientObject->setSecret($bcrypt->create($newPassword));
        }

        $clientObject = $this->clientService->update($clientObject);

        if ( ! $clientObject instanceof Client )
        {
            $this->flashMessenger()->addErrorMessage(
                'Error updating the client'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/client-manage' , ['client_id' => $client_id]);
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client updated'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
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

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
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
                $this->jwtForm->bind($jwtObject);
            }

            return new ViewModel(
                array(
                    'form' => $this->jwtForm,
                    'jwt_id' => $jwt_id
                )
            );

        }

        $this->jwtForm->setData($prg);

        if ( ! $this->jwtForm->isValid() ) {

            return new ViewModel(
                array( 'form' => $this->jwtForm , 'jwt_id' => $jwt_id ) );
        }

        $jwtObject = $this->jwtForm->getData();

        $jwtObject = $this->jwtService->update($jwtObject);

        if ( ! $jwtObject instanceof Jwt)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to save the jwt object'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client updated'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');

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

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        $result = $this->jwtService->delete($jwt_id);

        if (!$result)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to delete Key'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Key deleted'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');


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
                'You must first remove the public key before deleting the client'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        /**
         * Check for AccessTokens
         */

        $tokens = $this->accessTokenService->findByClient($client_id);

        foreach ($tokens AS $accessToken)
        {
            $this->accessTokenService->delete($accessToken);
        }

        $result = $this->clientService->delete($client_id);

        if (!$result)
        {
            $this->flashMessenger()->addErrorMessage(
                'Unable to delete client'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        $this->flashMessenger()->addSuccessMessage(
            'Client deleted'
        );

        return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');


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

        if ( ! $clientObject instanceof Client )
        {
            $this->flashMessenger()->addErrorMessage(
                'Missing client object'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
        }

        if ( ! $jwtObject instanceof Jwt )
        {
            $this->flashMessenger()->addErrorMessage(
                'Missing jwt object'
            );

            return $this->redirect()->toRoute('zf-oauth-doctrine-gui/clients');
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

        $privateKey = $prg['jwt']['privkey'];

        $iss = $prg['jwt']['iss'];
        $sub = $prg['jwt']['sub'];
        $aud = $prg['jwt']['aud'];
        $exp = $prg['jwt']['exp'];
        $nbf = $prg['jwt']['nbt'];
        $jti = $prg['jwt']['jti'];

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