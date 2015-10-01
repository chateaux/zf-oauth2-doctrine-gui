<?php
namespace DoctrineGui\Form;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

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
                'type'       => 'text',
                'options'    => [
                    'label' => 'Subject',
                    'instructions' => 'This is typically a user_id (it can be an admin user_id, or the user_id of the parties using this client.
                    Depending on how your write your API, you may want to retrieve this ID in order to customise the API response based on the user
                    associated to the API.'
                ],
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
                    'instructions' => 'Enter your public key here (if you are running tests, you can grab a test key from the utils/Key folder)'
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