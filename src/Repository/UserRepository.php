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
use Mailery\Widget\Search\Data\Reader\SelectDataReader;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository extends Repository implements IdentityRepositoryInterface
{
    /**
     * @param array $scope
     * @param array $orderBy
     * @return SelectDataReader
     */
    public function getDataReader(array $scope = [], array $orderBy = []): SelectDataReader
    {
        return new SelectDataReader($this->select()->where($scope)->orderBy($orderBy));
    }

    /**
     * @return Select
     */
    public function findActive(): Select
    {
        return $this->select()->where('status', 'active');
    }

    /**
     * @inheritdoc
     */
    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this
            ->findActive()
            ->where('id', $id)
            ->fetchOne();
    }

    /**
     * @inheritdoc
     */
    public function findIdentityByToken(string $token, string $type = null): ?IdentityInterface
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

    /**
     * @param string $email
     * @param User|null $exclude
     * @return User|null
     */
    public function findByEmail(string $email, ?User $exclude = null): ?User
    {
        return $this
            ->select()
            ->where(function (QueryBuilder $select) use ($email, $exclude) {
                $select->where('email', $email);

                if ($exclude !== null) {
                    $select->where('id', '<>', $exclude->getId());
                }
            })
            ->fetchOne();
    }

    /**
     * @param string $username
     * @param User|null $exclude
     * @return User|null
     */
    public function findByUsername(string $username, ?User $exclude = null): ?User
    {
        return $this
            ->select()
            ->where(function (QueryBuilder $select) use ($username, $exclude) {
                $select->where('username', $username);

                if ($exclude !== null) {
                    $select->where('id', '<>', $exclude->getId());
                }
            })
            ->fetchOne();
    }
}
