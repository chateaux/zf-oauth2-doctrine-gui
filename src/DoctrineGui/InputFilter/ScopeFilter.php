<?php
namespace DoctrineGui\InputFilter;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Zend\InputFilter\InputFilter;

class ScopeFilter extends InputFilter
{
    function __construct() {

        $this->add( array(
                'name'      => 'id',
                'required'  => false,
            )
        );

        $this->add( array(
                'name'      => 'scope',
                'required'  => true,
            )
        );

        $this->add( array(
                'name'      => 'isDefault',
                'required'  => true,
            )
        );

    }

}