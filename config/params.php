<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Mailery\User\Controller\DefaultController;
use Mailery\User\Controller\UserController;
use Mailery\Menu\MenuItem;
use Opis\Closure\SerializableClosure;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;

return [
    'cycle.common' => [
        'entityPaths' => [
            '@vendor/maileryio/mailery-user/src/Entity',
        ],
    ],

    'session' => [
        'options' => [
            'cookie_secure' => 0,
        ],
    ],

    'router' => [
        'routes' => [
            '/user/default/index' => Route::get('/user/default/index', [DefaultController::class, 'index'])
                ->name('/user/default/index'),
            '/user/default/view' => Route::get('/user/default/view/{id:\d+}', [DefaultController::class, 'view'])
                ->name('/user/default/view'),
            '/user/default/create' => Route::methods(['GET', 'POST'], '/user/default/create', [DefaultController::class, 'create'])
                ->name('/user/default/create'),
            '/user/default/edit' => Route::methods(['GET', 'POST'], '/user/default/edit/{id:\d+}', [DefaultController::class, 'edit'])
                ->name('/user/default/edit'),
            '/user/default/delete' => Route::delete('/user/default/delete/{id:\d+}', [DefaultController::class, 'delete'])
                ->name('/user/default/delete'),
            '/user/default/logout' => Route::post('/user/default/logout', [UserController::class, 'logout'])
                ->name('/user/default/logout'),
        ],
    ],

    'menu' => [
        'navbar' => [
            'items' => [
                'users' => (new MenuItem())
                    ->withLabel('Sign out')
                    ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
                        return $urlGenerator->generate('/user/default/logout');
                    })),
            ],
        ],
        'sidebar' => [
            'items' => [
                'users' => (new MenuItem())
                    ->withLabel('Users')
                    ->withIcon('users')
                    ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
                        return $urlGenerator->generate('/user/default/index');
                    })),
            ],
        ],
    ],
];
