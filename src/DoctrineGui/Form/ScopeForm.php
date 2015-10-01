<?php
namespace DoctrineGui\Form;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Zend\Form\Element;
use Zend\Form\Form;

class ScopeForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct('scope-form', $options);
    }

    public function init()
    {
        $this->add(
            [
                'name' => 'csrfcheck',
                'type' => 'csrf'
            ]
        );


        $this->add(
            [
                'name' => 'scope',
                'type' => ScopeFieldset::class,
                'options' => [
                    'use_as_base_fieldset' => true
                ]
            ]
        );

        $this->add(
            [
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => [
                    'value' => 'Create'
                ]
            ]
        );
    }
}