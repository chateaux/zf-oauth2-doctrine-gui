<?php
namespace DoctrineGui\Form\Factory;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use DoctrineGui\Form\ScopeForm;
use DoctrineGui\InputFilter\ScopeFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ScopeFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ScopeForm(
            $serviceLocator->getServiceLocator()->get('InputFilterManager')->get(ScopeFilter::class)
        );
    }
}