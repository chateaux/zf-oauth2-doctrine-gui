<?php
namespace DoctrineGui;

use Application\View\Helper\Navigation\DoctrineGuiNavHelperFactory;
use DoctrineGui\Controller\DoctrineGuiController;
use DoctrineGui\Controller\Factory\DoctrineGuiControllerFactory;
use DoctrineGui\Form\ClientFieldset;
use DoctrineGui\Form\ClientForm;
use DoctrineGui\Form\Factory\ClientFieldsetFactory;
use DoctrineGui\Form\Factory\ClientFormFactory;
use DoctrineGui\Form\Factory\JwtFieldsetFactory;
use DoctrineGui\Form\Factory\JwtFormFactory;
use DoctrineGui\Form\JwtFieldset;
use DoctrineGui\Form\JwtForm;
use DoctrineGui\InputFilter\ClientFilter;
use DoctrineGui\InputFilter\JwtFilter;
use DoctrineGui\Service\AccessTokenService;
use DoctrineGui\Service\ClientService;
use DoctrineGui\Service\Factory\AccessTokenServiceFactory;
use DoctrineGui\Service\Factory\ClientServiceFactory;
use DoctrineGui\Service\Factory\JwtServiceFactory;
use DoctrineGui\Service\Factory\ScopeServiceFactory;
use DoctrineGui\Service\JwtService;
use DoctrineGui\Service\ScopeService;
use DoctrineGui\View\Helper\FlashMessengerHelper;

return [
    'controllers' => [
        'factories' => [
            DoctrineGuiController::class => DoctrineGuiControllerFactory::class,
        ]
    ],
    'service_manager'    => [
        'factories'  => [
            ClientService::class => ClientServiceFactory::class,
            JwtService::class => JwtServiceFactory::class,
            ScopeService::class => ScopeServiceFactory::class,
            AccessTokenService::class => AccessTokenServiceFactory::class
        ]
    ],
    'form_elements'      => [
        'factories'  => [
            ClientForm::class => ClientFormFactory::class,
            ClientFieldset::class => ClientFieldsetFactory::class,
            JwtFieldset::class => JwtFieldsetFactory::class,
            JwtForm::class => JwtFormFactory::class
        ]
    ],
    'input_filters'      => [
        'invokables' => [
            'ClientFilter' => ClientFilter::class,
            'JwtClientFilter' => JwtFilter::class
        ]
    ],
    'view_helpers'  => [
        'invokables'    => [
            'FlashMessengerHelper'  => FlashMessengerHelper::class
        ],
        'factories'     => [
            'DoctrineGuiNavHelper'  => DoctrineGuiNavHelperFactory::class,
        ]
    ],
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
    //Little faster setting templates like this
    'view_manager'       => [
        'template_map' => [
            'doctrine-gui/overview'      => __DIR__ . '/../view/overview.phtml',
            'doctrine-gui/games'         => __DIR__ . '/../view/games.phtml',
            'doctrine-gui/clients'       => __DIR__ . '/../view/clients.phtml',
            'doctrine-gui/test-jwt'      => __DIR__ . '/../view/test-jwt.phtml',
            'doctrine-guiclient-manage'  => __DIR__ . '/../view/client-manage.phtml',
            'doctrine-gui/manage-key'    => __DIR__ . '/../view/manage-key.phtml',
        ]
    ],
    'asset_manager' => [
        'caching' => [
            'default' => [
                'cache'     => 'FilePath',  // Apc, FilePath, FileSystem etc.
                'options' => [
                    'dir'   => 'public'
                ]
            ],
        ],
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../assets',
            ]

        ],
    ],
    'router'             => [
        'routes' => [
            'zf-oauth-doctrine-gui' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/zf-oauth-doctrine-gui',

                ],
                'may_terminate' => false,
                'child_routes' => [
                    'overview' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/overview',
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'overview',
                            ],
                        ]
                    ],
                    'clients' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/clients',
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'clients',
                            ],
                        ]
                    ],
                    'test-jwt' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/test-jwt/:jwt_id/:client_id',
                            'constraints' => [
                                'jwt_id' => '[0-9]+',
                                'client_id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'test-jwt',
                            ],
                        ]
                    ],
                    'manage-key' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/manage-key[/:client_id]/:jwt_id',
                            'constraints' => [
                                'jwt_id' => '[0-9]+',
                                'client_id' => '[0-9]+'
                            ],
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'manage-key',
                            ],
                        ]
                    ],
                    'delete-jwt-key' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/delete-jwt-key/:jwt_id',
                            'constraints' => [
                                'jwt_id' => '[0-9]+'
                            ],
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'delete-jwt-key',
                            ],
                        ]
                    ],
                    'delete-client' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/delete-client/:client_id',
                            'constraints' => [
                                'client_id' => '[0-9]+'
                            ],
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'delete-client',
                            ],
                        ]
                    ],
                    'client-manage' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/client-manage/[:client_id]',
                            'constraints' => [
                                'client_id' => '[0-9]+'
                            ],
                            'defaults' => [
                                'controller' => DoctrineGuiController::class,
                                'action'     => 'client-manage',
                            ],
                        ]
                    ]


                ]
            ]
        ]
    ]
];