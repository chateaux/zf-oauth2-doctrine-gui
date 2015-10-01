<?php
namespace DoctrineGui\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class GenerateJwtFieldset extends Fieldset
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @access protected
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param Jwt $jwtPrototype
     * @param null $name
     * @param array $options
     */
    public function __construct(
        ObjectManager $objectManager,
        Jwt $jwtPrototype,
        $name = null,
        $options = []
    ) {
        parent::__construct($name, $options);
        $this->objectManager = $objectManager;
        $this->setHydrator(new DoctrineObject($objectManager));
        $this->setObject($jwtPrototype);
    }

    public function init()
    {

        $this->add(
            [
                'name' => 'privkey',
                'type' => 'textarea',
                'attributes' => array(
                    'required'    => 'required',
                    'class'       => 'form-control',
                    'rows'        => 5,
                    'cols'        => 40
                ),
                'options'    => [
                    'label' => 'Private key',
                    'instructions' => 'Make sure you use test public/private key pairs here (we have provided test keys in the utils/Keys directory)',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'iss',
                'type' => 'text',
                'attributes' => array(
                    'required'    => 'required',
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'The issuer',
                    'instructions' => 'Usually the client id',
                ],
            ]
        );


        $this->add(
            [
                'name' => 'sub',
                'type' => 'text',
                'attributes' => array(
                    'required'    => 'required',
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'The subject',
                    'instructions' => 'Usually the user id',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'aud',
                'type' => 'text',
                'attributes' => array(
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'The audience',
                    'instructions' => 'Usually uri for the oauth server (not required)',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'exp',
                'type' => 'text',
                'attributes' => array(
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'The expiration date',
                    'instructions' => 'In seconds since epoch. If the current time is greater than the exp, the JWT is invalid (not required)',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'nbt',
                'type' => 'text',
                'attributes' => array(
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'Not Bearer time',
                    'instructions' => 'In seconds since epoch. If the current time is less than the nbf, the JWT is invalid (not required)',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'jti',
                'type' => 'text',
                'attributes' => array(
                    'class'       => 'form-control',
                ),
                'options'    => [
                    'label' => 'Jti',
                    'instructions' => 'The "jwt token identifier", or nonce for this JWT (not required)',
                ],
            ]
        );

        $this->add(
            [
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'scope',
                'options' => [
                    'object_manager' => $this->objectManager,
                    'target_class'   => 'ZF\OAuth2\Doctrine\Entity\Scope',
                    'property'       => 'scope',
                    'label' => 'Scope',
                    'instructions' => 'What scopes should be applied to the tokens that this JWT will return?'
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