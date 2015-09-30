<?php
namespace DoctrineGui\Form\Factory;

use DoctrineAdmin\Form\ClientForm;
use DoctrineAdmin\InputFilter\ClientFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ClientFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ClientForm(
            $serviceLocator->getServiceLocator()->get('InputFilterManager')->get(ClientFilter::class)
        );
    }
}