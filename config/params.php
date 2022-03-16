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
use Mailery\Menu\MenuItem;
use Yiisoft\Definitions\DynamicReference;
use Mailery\User\Entity\User;

return [
    'yiisoft/yii-console' => [
        'commands' => [
            'user/create' => CreateCommand::class,
            'user/assignRole' => AssignRoleCommand::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        'entity-paths' => [
            '@vendor/maileryio/mailery-user/src/Entity',
        ],
    ],

    'maileryio/mailery-activity-log' => [
        'entity-groups' => [
            'user' => [
                'label' => DynamicReference::to(static fn () => 'User'),
                'entities' => [
                    User::class,
                ],
            ],
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
                            return strtok($urlGenerator->generate('/user/default/index'), '?');
                        },
                    ],
                ],
            ],
            'profile' => [
                'label' => static function () {
                    return 'My profile';
                },
                'items' => [
                    'profile' => [
                        'label' => static function () {
                            return 'View profile';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return '#';
                        },
                    ],
                    'settings' => [
                        'label' => static function () {
                            return 'Account settings';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return '#';
                        },
                    ],
                    'logout' => [
                        'label' => static function () {
                            return 'Logout';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return strtok($urlGenerator->generate('/user/auth/logout'), '?');
                        },
                        'method' => MenuItem::METHOD_POST,
                    ],
                ],
            ],
        ],
    ],
];
