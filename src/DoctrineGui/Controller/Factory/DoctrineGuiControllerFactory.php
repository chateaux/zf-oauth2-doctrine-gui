<?php
namespace DoctrineGui\Controller\Factory;

use DoctrineGui\Controller\DeveloperController;
use DoctrineGui\Form\ClientForm;
use DoctrineGui\Form\GenerateJwtForm;
use DoctrineGui\Form\JwtForm;
use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\JwtService;
use DoctrineGui\Service\OauthClientService;
use DoctrineGui\Service\ScopeService;
use Games\Service\GamePublisherService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Games\Service\GameServiceInterface;
use ZF\OAuth2\Doctrine\Adapter\DoctrineAdapter;
use ZfcRbac\Service\AuthorizationService;

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

        return new DeveloperController(
            $realSl->get(ClientService::class),
            $realSl->get(JwtService::class),
            $realSl->get(ScopeService::class),
            $realSl->get(AccessTokenService::class),
            $realSl->get(DoctrineAdapter::class),
            $realSl->get('FormElementManager')->get(ClientForm::class),
            $realSl->get('FormElementManager')->get(JwtForm::class),
            $realSl->get('FormElementManager')->get(GenerateJwtForm::class),
            $realSl->get(ApplicationSettingsInterface::class)
        );
    }
}