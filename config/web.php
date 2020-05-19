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
use Mailery\User\Entity\User as UserEntity;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Yii\Web\Session\Session;
use Yiisoft\Yii\Web\Session\SessionInterface;
use Yiisoft\Yii\Web\User\User as WebUser;

$navbarSystem = $params['menu']['navbar']['items']['system'];
$navbarSystemChilds = $navbarSystem->getChildItems();
$navbarSystemChilds['users'] = $params['usersNavbarMenuItem'];
$navbarSystem->setChildItems($navbarSystemChilds);

return [
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

        return new WebUser($identityRepository, $eventDispatcher, $session);
    },
];
