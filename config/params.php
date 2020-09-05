<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Mailery\Menu\MenuItem;
use Mailery\User\Controller\DefaultController;
use Mailery\User\Controller\UserController;
use Opis\Closure\SerializableClosure;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;

return [
    'usersNavbarMenuItem' => (new MenuItem())
        ->withLabel('Users')
        ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
            return $urlGenerator->generate('/user/default/index');
        }))
        ->withOrder(100),

    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-user/src/Entity',
        ],
    ],

    'session' => [
        'options' => [
            'cookie_secure' => 0,
        ],
    ],

    'menu' => [
        'navbar' => [
            'items' => [
                'profile' => (new MenuItem())
                    ->withLabel('My profile')
                    ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
                        return $urlGenerator->generate('/user/default/logout');
                    }))
                    ->withOrder(300),
            ],
        ],
    ],
];
