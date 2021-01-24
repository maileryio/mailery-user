<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Mailery\User\Repository\UserRepository;
use Yiisoft\Auth\IdentityRepositoryInterface;

$navbarSystem = $params['menu']['navbar']['items']['system'];
$navbarSystemChilds = $navbarSystem->getChildItems();
$navbarSystemChilds['users'] = $params['usersNavbarMenuItem'];
$navbarSystem->setChildItems($navbarSystemChilds);

return [
    IdentityRepositoryInterface::class => UserRepository::class,
];
