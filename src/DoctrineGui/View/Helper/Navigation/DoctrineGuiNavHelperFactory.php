<?php
namespace DoctrineGui\View\Helper\Navigation;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineGuiNavHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realSL = $serviceLocator->getServiceLocator();

        return new DoctrineGuiNavHelper(
            $realSL->get('zfcrbacserviceauthorizationservice')
        );
    }

}