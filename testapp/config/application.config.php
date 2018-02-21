<?php

return [
    'modules' => [
        'Zend\Router',
        'Application',
    ],
    'module_listener_options' => [
        'module_paths' => [
            __DIR__ . '/../module',
        ],
    ],
];
