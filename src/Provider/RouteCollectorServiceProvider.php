<?php

namespace Mailery\User\Provider;

use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\User\Controller\DefaultController;
use Mailery\User\Controller\UserController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/user',
                [
                    Route::get('/default/index', [DefaultController::class, 'index'])
                        ->name('/user/default/index'),
                    Route::get('/default/view/{id:\d+}', [DefaultController::class, 'view'])
                        ->name('/user/default/view'),
                    Route::methods(['GET', 'POST'], '/default/create', [DefaultController::class, 'create'])
                        ->name('/user/default/create'),
                    Route::methods(['GET', 'POST'], '/default/edit/{id:\d+}', [DefaultController::class, 'edit'])
                        ->name('/user/default/edit'),
                    Route::delete('/default/delete/{id:\d+}', [DefaultController::class, 'delete'])
                        ->name('/user/default/delete'),

                    Route::post('/login', [UserController::class, 'login'])
                        ->name('/user/default/login'),
                    Route::post('/logout', [UserController::class, 'logout'])
                        ->name('/user/default/logout'),
                ]
            )
        );
    }
}
