<?php

namespace Application;

use Application\Controller\IndexController;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module
{
    public function getConfig()
    {
        return [
            'router' => [
                'routes' => [
                    'home' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/',
                            'defaults' => [
                                'controller' => IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'controllers' => [
                'factories' => [
                    IndexController::class => InvokableFactory::class,
                ],
            ],
            'view_manager' => [
                'display_not_found_reason' => true,
                'display_exceptions'       => true,
                'doctype'                  => 'HTML5',
                'not_found_template'       => 'error/error',
                'exception_template'       => 'error/error',
                'template_map' => [
                    'error/error'  => __DIR__ . '/view/error/error.phtml',
                    'layout/layout' => __DIR__ . '/view/layout/layout.phtml',
                    'application/index/index' => __DIR__ . '/view/application/index/index.phtml',
                ],
            ],
        ];
    }
}
