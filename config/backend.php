<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Cycle\ORM\ORMInterface;
use Mailery\Menu\Sidebar\SidebarMenuInterface;
use Mailery\User\Entity\User as UserEntity;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Web\Session\Session;
use Yiisoft\Yii\Web\Session\SessionInterface;
use Yiisoft\Yii\Web\User\User as WebUser;

return [
    SidebarMenuInterface::class => [
        'setItems()' => [
            'items' => [
                'users' => [
                    'label' => function () {
                        return 'Users';
                    },
                    'icon' => 'users',
                    'url' => function (ContainerInterface $container) {
                        return $container->get(UrlGeneratorInterface::class)
                            ->generate('/user/default/index');
                    },
                ],
            ],
        ],
    ],
    SessionInterface::class => [
        '__class' => Session::class,
        '__construct()' => [
            $params['session']['options'] ?? [],
            $params['session']['handler'] ?? null,
        ],
    ],
    IdentityRepositoryInterface::class => function (ContainerInterface $container) {
        $orm = $container->get(ORMInterface::class);

        return $orm->getRepository(UserEntity::class);
    },
    WebUser::class => function (ContainerInterface $container) {
        $session = $container->get(SessionInterface::class);
        $identityRepository = $container->get(IdentityRepositoryInterface::class);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $webUser = new WebUser($identityRepository, $eventDispatcher);
        $webUser->setSession($session);

        return $webUser;
    },
];
