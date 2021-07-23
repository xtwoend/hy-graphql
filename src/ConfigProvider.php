<?php

namespace Xtwoend\HyGraphQL;


class ConfigProvider 
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                // 
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for graphql.',
                    'source' => __DIR__ . '/../publish/graphql.php',
                    'destination' => BASE_PATH . '/config/autoload/graphql.php',
                ],
            ],
        ];
    }
}