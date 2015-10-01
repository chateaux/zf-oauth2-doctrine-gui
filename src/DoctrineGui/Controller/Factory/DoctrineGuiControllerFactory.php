<?php
namespace DoctrineGui\Controller\Factory;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use DoctrineGui\Controller\DoctrineGuiController;
use DoctrineGui\Form\ClientForm;
use DoctrineGui\Form\GenerateJwtForm;
use DoctrineGui\Form\JwtForm;
use DoctrineGui\Form\ScopeForm;
use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\JwtService;
use DoctrineGui\Service\ScopeService;
use ZF\OAuth2\Doctrine\Adapter\DoctrineAdapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineGuiControllerFactory implements FactoryInterface
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

        return new DoctrineGuiController(
            $realSl->get(ClientService::class),
            $realSl->get(JwtService::class),
            $realSl->get(ScopeService::class),
            $realSl->get(AccessTokenService::class),
            $realSl->get(DoctrineAdapter::class),
            $realSl->get('FormElementManager')->get(ClientForm::class),
            $realSl->get('FormElementManager')->get(JwtForm::class),
            $realSl->get('FormElementManager')->get(GenerateJwtForm::class),
            $realSl->get('FormElementManager')->get(ScopeForm::class)
        );
    }
}