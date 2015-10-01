<?php
namespace DoctrineGui\Form\Factory;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use DoctrineGui\Form\ScopeFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Scope;

class ScopeFieldsetFactory implements FactoryInterface
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

        return new ScopeFieldset(
            $realSl->get('Doctrine\ORM\EntityManager'),
            new Scope()
        );
    }
} 