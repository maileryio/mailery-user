<?php

use Mailery\User\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Mailery\User\Entity\User;
use Mailery\User\Setting\UserSettingGroup;
use Psr\Container\ContainerInterface;

return [
    UserRepository::class => static function (ContainerInterface $container) {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(User::class);
    },

    UserSettingGroup::class => [
        '__construct()' => [
            'items' => $params['maileryio/mailery-setting']['groups']['user']['items'],
            'order' => $params['maileryio/mailery-setting']['groups']['user']['order'] ?? 0,
        ],
    ],
];
