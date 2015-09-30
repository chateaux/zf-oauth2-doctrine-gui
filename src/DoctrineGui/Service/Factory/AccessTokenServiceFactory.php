<?php
namespace DoctrineGui\Service\Factory;

use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\ScopeService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\AccessToken;

class AccessTokenServiceFactory implements FactoryInterface
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

        return new AccessTokenService(
            $objectManager,
            $objectManager->getRepository(AccessToken::class),
            $serviceLocator->get(ScopeService::class),
            $serviceLocator->get(ClientService::class)
        );
    }
}