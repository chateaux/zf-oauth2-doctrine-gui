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
use Zend\InputFilter\InputFilterInterface;

class JwtForm extends Form
{
    /**
     * @var \Zend\InputFilter\InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @param InputFilterInterface $inputFilter
     * @param null $name
     * @param array $options
     */
    public function __construct(
        InputFilterInterface $inputFilter,
        $name = null,
        $options = array()
    ) {
        parent::__construct('jwt-form', $options);
        $this->jwtClientFilter = $inputFilter;
    }

    public function init()
    {
        $this->add(
            [
                'name' => 'csrfcheck',
                'type' => 'csrf',
                'options' => [
                    'csrf_options' => array(
                        'message' => 'The form verification credentials have expired, please re-submit the form.',
                        'timeout' => 1200
                    )
                ]
            ]
        );

        $this->add(
            [
                'name' => 'jwt',
                'type' => JwtFieldset::class,
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
                    'value' => 'Register'
                ]
            ]
        );

        $this->getInputFilter()->add($this->jwtClientFilter, 'jwt');

    }
}