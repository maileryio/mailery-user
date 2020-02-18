<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Repository;

use Cycle\ORM\Select;
use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\Repository;
use Mailery\User\Entity\User;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository extends Repository implements IdentityRepositoryInterface
{
    /**
     * @return Select
     */
    public function findActive(): Select
    {
        return $this->select()->where('status', 'active');
    }

    /**
     * @param string $id
     * @return IdentityInterface|null
     */
    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this
            ->findActive()
            ->where('id', $id)
            ->fetchOne();
    }

    /**
     * @param string $token
     * @param string $type
     * @return IdentityInterface|null
     */
    public function findIdentityByToken(string $token, string $type): ?IdentityInterface
    {
        throw new \RuntimeException('"findIdentityByAccessToken" is not implemented with params: token[' . $token . '] and type[' . $type . '].');
    }

    /**
     * @param string $login
     * @return User|null
     */
    public function findByLogin(string $login): ?User
    {
        return $this
            ->findActive()
            ->where(function (QueryBuilder $select) use ($login) {
                $select->where('email', $login)->orWhere('username', $login);
            })
            ->fetchOne();
    }
}
