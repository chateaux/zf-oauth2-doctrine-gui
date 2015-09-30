<?php
namespace DoctrineGui\Service\Factory;

use DoctrineGui\Service\ScopeService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Scope;

class ScopeServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $objectManager = $serviceLocator->get('Doctrine\ORM\EntityManager');

        return new ScopeService(
            $objectManager,
            $objectManager->getRepository(Scope::class)
        );
    }
}