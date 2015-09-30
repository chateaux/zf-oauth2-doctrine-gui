<?php
namespace DoctrineGui\Service\Factory;

use DoctrineGui\Service\ClientService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Client;

class ClientServiceFactory implements FactoryInterface
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

        return new ClientService(
            $objectManager,
            $objectManager->getRepository(Client::class)
        );
    }
}