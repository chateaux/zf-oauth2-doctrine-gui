<?php
namespace DoctrineGui\InputFilter;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Zend\InputFilter\InputFilter;

class ClientFilter extends InputFilter
{
    function __construct() {

        $this->add( array(
                'name'      => 'id',
                'required'  => false,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add( array(
                'name'      => 'user',
                'required'  => true,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add( array(
                'name'      => 'clientId',
                'required'  => true,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add( array(
                'name'      => 'redirectUri',
                'required'  => true,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add( array(
                'name'      => 'grantType',
                'required'  => true,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add( array(
                'name'      => 'scope',
                'required'  => false,
                'filters'   => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $this->add(
            [
                'name'       => 'password',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 128
                        ]
                    ]
                ]
            ]
        );

        $this->add(
            [
                'name'       => 'passwordRepeat',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 128
                        ]
                    ]
                ]
            ]
        );



    }

}