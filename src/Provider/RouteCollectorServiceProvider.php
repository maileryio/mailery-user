<?php

namespace Mailery\User\Provider;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\User\Controller\DefaultController;
use Mailery\User\Controller\AuthController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
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

                    Route::methods(['GET', 'POST'], '/login', [AuthController::class, 'login'])
                        ->name('/user/auth/login'),
                    Route::post('/logout', [AuthController::class, 'logout'])
                        ->name('/user/auth/logout'),
                ]
            )
        );
    }
}
