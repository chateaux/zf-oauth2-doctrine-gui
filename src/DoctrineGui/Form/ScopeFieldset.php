<?php
namespace DoctrineGui\Form;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use ZF\OAuth2\Doctrine\Entity\Scope;

class ScopeFieldset extends Fieldset
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @access protected
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param Scope $clientPrototype
     * @param null $name
     * @param array $options
     */
    public function __construct(
        ObjectManager $objectManager,
        Scope $clientPrototype,
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

        $this->add(
            [
                'name'       => 'scope',
                'type'       => 'text',
                'options'    => [
                    'label' => 'Scope',
                    'instructions' => 'The name of your scope: basic, login, register, email-access (keep them lowercase and use hyphens instead of spaces)'
                ],
                'attributes' => [
                    'required'    => 'required',
                ]
            ]
        );

        $this->add(
            [
                'name'       => 'isDefault',
                'type'       => 'select',
                'options'    => [
                    'label' => 'Make a Default',
                    'instructions' => 'When a client creates a token, default scopes are assigned to that token. If for instance you make admin-access as a default scope, then anyone issued with an access token will in theory have admin-access... so in general, keep the default as: basic.'
                ],
                'attributes' => [
                    'options'  => [
                        0 => 'n/a',
                        1 => 'Default',
                    ],
                ]
            ]
        );


    }
} 