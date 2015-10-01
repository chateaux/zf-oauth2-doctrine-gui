<?php
namespace DoctrineGui\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use ZF\OAuth2\Doctrine\Entity\Client;

class ClientFieldset extends Fieldset
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @access protected
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param Client $clientPrototype
     * @param null $name
     * @param array $options
     */
    public function __construct(
        ObjectManager $objectManager,
        Client $clientPrototype,
        $name = null,
        $options = []
    ) {
        parent::__construct($name, $options);
        $this->objectManager = $objectManager;
        $this->setHydrator(new DoctrineObject($objectManager));
        $this->setObject($clientPrototype);
    }

    public function init()
    {
        $this->add([
                'name'       => 'id',
                'type'       => 'hidden',
            ]
        );

        $this->add([
                'name'          => 'clientId',
                'attributes'    => [
                    'required' => 'required',
                    'type' => 'text',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => 'Client identity',
                    'instructions' => 'Enter a client identity'
                ],
            ]);

        $this->add(
            [
                'name'       => 'password',
                'type'       => 'password',
                'options'    => [
                    'label' => 'Password',
                    'instructions' => 'Leave blank if you do not wish to update your client secret'
                ],
                'attributes' => [
                    'class' => 'form-control',
                ]
            ]
        );

        $this->add(
            [
                'name'       => 'passwordRepeat',
                'type'       => 'password',
                'options'    => [
                    'label' => 'Repeat Password',
                    'instructions' => 'Leave blank if you do not wish to update your client secret'
                ],
                'attributes' => [
                    'class' => 'form-control',
                ]
            ]
        );

        $this->add([
                'name'          => 'redirectUri',
                'attributes'    => [
                    'required' => 'required',
                    'type' => 'text',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => 'Redirect uri',
                    'instructions' => 'The uri this client will re-direct back to'
                ],
            ]);

        $this->add([
                'name' => 'grantType',
                'type' => 'select',
                'attributes'    => [
                    'required' => 'required',
                    'class' => 'form-control',
                    'options'  => [
                        'implicit' => 'implicit',
//                        'authorization_code' => 'authorization_code',
//                        'access_token' => 'access_token',
//                        'refresh_token' => 'refresh_token',
                        'urn:ietf:params:oauth:grant-type:jwt-bearer' => 'urn:ietf:params:oauth:grant-type:jwt-bearer'
                    ],
                    'multiple' => true,
                ],
                'options' => [
                    'label' => 'Grant type',
                    'instructions' => 'Enter the grant type required for this client. Implicit for SSO and urn:ietf... for Json Web Tokens (server to server)'
                ],
            ]);

        $this->add(
            [
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'scope',
                'options' => [
                    'object_manager' => $this->objectManager,
                    'target_class'   => 'ZF\OAuth2\Doctrine\Entity\Scope',
                    'property'       => 'scope',
                    'label' => 'Scope',
                    'instructions' => 'Scope is used to limit access of a client. It is advisable to create scopes and to check the inbound clients scope level before authorising access to your API end point.'
                ],
                'attributes' => [
                    'required' => 'required',
                    'multiple' => true,
                    'class' => 'form-control',
                ]
            ]
        );




    }
} 