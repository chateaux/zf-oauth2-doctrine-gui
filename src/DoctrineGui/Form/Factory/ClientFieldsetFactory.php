<?php
namespace DoctrineGui\Form\Factory;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use DoctrineGui\Form\ClientFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Doctrine\Entity\Client;

class ClientFieldsetFactory implements FactoryInterface
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

        return new ClientFieldset(
            $realSl->get('Doctrine\ORM\EntityManager'),
            new Client()
        );
    }
} 