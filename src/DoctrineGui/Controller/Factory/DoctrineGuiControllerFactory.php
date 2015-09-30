<?php
namespace DoctrineGui\Controller\Factory;

use DoctrineGui\Controller\DoctrineGuiController;
use DoctrineGui\Form\ClientForm;
use DoctrineGui\Form\GenerateJwtForm;
use DoctrineGui\Form\JwtForm;
use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\JwtService;
use DoctrineGui\Service\ScopeService;
use ZF\OAuth2\Doctrine\Adapter\DoctrineAdapter;

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
            $realSl->get('FormElementManager')->get(GenerateJwtForm::class)
        );
    }
}