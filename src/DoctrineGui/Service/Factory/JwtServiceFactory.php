<?php
namespace DoctrineGui\Service\Factory;

use DoctrineGui\Service\JwtService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class JwtServiceFactory implements FactoryInterface
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

        return new JwtService(
            $objectManager,
            $objectManager->getRepository(Jwt::class)
        );
    }
}