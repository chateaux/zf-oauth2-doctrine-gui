<?php
namespace DoctrineGui\Form\Factory;

use DoctrineAdmin\Form\JwtFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class JwtFieldsetFactory implements FactoryInterface
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

        return new JwtFieldset(
            $realSl->get('Doctrine\ORM\EntityManager'),
            new Jwt()
        );
    }
} 