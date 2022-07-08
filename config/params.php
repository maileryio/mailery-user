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
use Mailery\User\Entity\User;
use Mailery\User\Setting\UserSettingGroup;
use Mailery\Menu\MenuItem;
use Mailery\Setting\Form\SettingForm;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Form\Field;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;

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

    'maileryio/mailery-setting' => [
        'groups' => [
            'user' => [
                'reference' => Reference::to(UserSettingGroup::class),
                'items' => [
                    UserSettingGroup::PARAM_DEFAULT_COUNTRY => [
                        'name' => UserSettingGroup::PARAM_DEFAULT_COUNTRY,
                        'label' => static function () {
                            return 'Default user country';
                        },
                        'description' => static function () {
                            return 'Default two-letter country code';
                        },
                        'field' => static function (SettingForm $form) {
                            return Field::text($form, UserSettingGroup::PARAM_DEFAULT_COUNTRY);
                        },
                        'rules' => static function () {
                            return [
                                Required::rule(),
                                HasLength::rule()->max(255),
                            ];
                        },
                        'value' => 'UA',
                    ],
                    UserSettingGroup::PARAM_DEFAULT_TIMEZONE => [
                        'name' => UserSettingGroup::PARAM_DEFAULT_TIMEZONE,
                        'label' => static function () {
                            return 'Default user timezone';
                        },
                        'description' => static function () {
                            return 'This timezone is used as the default time zone for creating users';
                        },
                        'field' => static function (SettingForm $form) {
                            return Field::text($form, UserSettingGroup::PARAM_DEFAULT_TIMEZONE);
                        },
                        'rules' => static function () {
                            return [
                                Required::rule(),
                                HasLength::rule()->max(255),
                            ];
                        },
                        'value' => 'Europe/Kiev',
                    ],
                ],
            ],
        ],
    ],
];
