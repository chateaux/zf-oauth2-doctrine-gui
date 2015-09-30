<?php
namespace DoctrineGui\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class GenerateJwtForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct('generate-jwt-form', $options);
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
                'name' => 'jwt',
                'type' => GenerateJwtFieldset::class,
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