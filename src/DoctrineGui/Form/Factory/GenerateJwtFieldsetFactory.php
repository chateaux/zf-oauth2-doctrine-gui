<?php
namespace DoctrineGui\Form\Factory;

use DoctrineGui\Form\GenerateJwtFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class GenerateJwtFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $realSl = $serviceLocator->getServiceLocator();

        return new GenerateJwtFieldset(
            $realSl->get('Doctrine\ORM\EntityManager'),
            new Jwt()
        );
    }
} 