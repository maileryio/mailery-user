<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Mailery\User\Entity\User;
use Psr\Container\ContainerInterface;
use Cycle\ORM\ORMInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Injector\Injector;

return [
    Authentication::class => static fn (UrlGeneratorInterface $urlGenerator, Injector $injector) => $injector->make(Authentication::class)
        ->withOptionalPatterns([
            $urlGenerator->generate('/user/auth/login'),
        ]),
    IdentityRepositoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(ORMInterface::class)->getRepository(User::class);
    },
];
