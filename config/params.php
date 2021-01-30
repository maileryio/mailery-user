<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Yiisoft\Router\UrlGeneratorInterface;
use Mailery\User\Console\CreateCommand;
use Mailery\User\Console\AssignRoleCommand;

return [
    'yiisoft/user' => [
        'authUrl' => '/user/login',
    ],

    'yiisoft/yii-console' => [
        'commands' => [
            'user/create' => CreateCommand::class,
            'user/assignRole' => AssignRoleCommand::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-user/src/Entity',
        ],
    ],

    'maileryio/mailery-menu-navbar' => [
        'items' => [
            'system' => [
                'items' => [
                    'users' => [
                        'label' => static function () {
                            return 'Users';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return $urlGenerator->generate('/user/default/index');
                        },
                        'order' => 100,
                    ],
                ],
            ],
            'profile' => [
                'label' => static function () {
                    return 'My profile';
                },
                'url' => static function (UrlGeneratorInterface $urlGenerator) {
                    return $urlGenerator->generate('/user/auth/logout');
                },
                'order' => 300,
            ],
        ],
    ],
];
