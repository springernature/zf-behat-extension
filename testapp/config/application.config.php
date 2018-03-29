<?php
 $config = [
    'modules' => [
        'Application',
    ],
    'module_listener_options' => [
        'module_paths' => [
            __DIR__ . '/../module',
        ],
    ],
];

if (class_exists('Zend\Router\Module')) {
    array_unshift($config['modules'], 'Zend\Router');
}
return $config;
