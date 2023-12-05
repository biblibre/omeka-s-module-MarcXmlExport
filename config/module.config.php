<?php

namespace MarcXmlExport;

return [
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'marc_xml_export_exports' => Api\Adapter\ExportAdapter::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            'MarcXmlExport\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'MarcXmlExport\Form\ExportForm' => Service\Form\ExportFormFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'ExportDetail' => View\Helper\ExportDetail::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'MarcXmlExport\Exporter' => Service\Exporter\ExporterFactory::class,
            'MarcXmlExport\Manager' => Service\MappingManagerFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Export Marc XML',
                'route' => 'admin/marcxml-export',
                'resource' => 'MarcXmlExport\Controller\Admin\Index',
                'pages' => [
                    [
                        'label' => 'Past Exports', //@translate
                        'route' => 'admin/marcxml-export',
                        'resource' => 'MarcXmlExport\Controller\Admin\Index',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'marcxml-export' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/marcxml-export',
                            'defaults' => [
                                '__NAMESPACE__' => 'MarcXmlExport\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                            'child_routes' => [
                                'export' => [
                                    'type' => 'Literal',
                                    'options' => [
                                        'route' => '/new',
                                        'defaults' => [
                                            '__NAMESPACE__' => 'MarcXmlExport\Controller\Admin',
                                            'controller' => 'Index',
                                            'action' => 'new',
                                        ],
                                    ],
                                ],
                                'save' => [
                                    'type' => 'Literal',
                                    'options' => [
                                        'route' => '/save',
                                        'defaults' => [
                                            '__NAMESPACE__' => 'MarcXmlExport\Controller\Admin',
                                            'controller' => 'Index',
                                            'action' => 'save',
                                        ],
                                    ],
                                ],
                            ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'marcxmlexport_mapping' => [
            'factories' => [
                'unimarc_standard' => Service\UnimarcStandardMappingFactory::class,
            ],
    ],

];
