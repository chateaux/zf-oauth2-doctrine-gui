<?php
namespace DoctrineGui\Form;

use DoctrineGui\Form\View\Helper\MyObjectHidden;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use ZF\OAuth2\Doctrine\Entity\Client;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class JwtFieldset extends Fieldset
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
        $this->add([
                'name'       => 'id',
                'type'       => 'hidden',
            ]
        );

        /**
         * Identity of the oauth client
         */
        $this->add(
            [
                'name'    => 'client',
                'type'    => MyObjectHidden::class,
                'options' => [
                    'object_manager' => $this->objectManager,
                    'target_class'   => Client::class
                ]
            ]
        );

        /**
         * User id saved here
         */
        $this->add(
            [
                'name'       => 'subject',
                'type'       => 'hidden',
            ]
        );

        /**
         * Game servers public key here
         */
        $this->add(
            [
                'name'       => 'publicKey',
                'type'       => 'textarea',
                'options'    => [
                    'label' => 'Public key',
                    'instructions' => 'Enter your game servers public key here'
                ],
                'attributes' => [
                    'class' => 'form-control',
                    'cols' => 100,
                    'rows' => 10,
                    'required' => 'required'
                ]
            ]
        );

    }
} 