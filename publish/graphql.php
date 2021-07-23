<?php

use Xtwoend\HyGraphQL\AuthenticationService;

return [
    'mode' => 'dev',
    'class_map' => [
        'controller' => 'App\\GraphQL\\Query',
        'type' => 'App\\GraphQL\\Entity'
    ],
    'guard' => [
        'authentication' => AuthenticationService::class,
        'authorization' => null
    ]
];