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
use Yiisoft\Router\Route;

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
            Route::get('/user/default/index', [DefaultController::class, 'index'])
                ->name('/user/default/index'),
            Route::get('/user/default/view/{id:\d+}', [DefaultController::class, 'view'])
                ->name('/user/default/view'),
            Route::methods(['GET', 'POST'], '/user/default/create', [DefaultController::class, 'create'])
                ->name('/user/default/create'),
            Route::methods(['GET', 'POST'], '/user/default/edit/{id:\d+}', [DefaultController::class, 'edit'])
                ->name('/user/default/edit'),
            Route::delete('/user/default/delete/{id:\d+}', [DefaultController::class, 'delete'])
                ->name('/user/default/delete'),
        ],
    ],
];
