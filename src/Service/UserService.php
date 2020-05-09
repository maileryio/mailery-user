<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\User\Entity\User;
use Mailery\User\ValueObject\UserValueObject;

class UserService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param UserValueObject $valueObject
     * @return User
     */
    public function create(UserValueObject $valueObject): User
    {
        $user = (new User())
            ->setEmail($valueObject->getEmail())
            ->setUsername($valueObject->getUsername())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($user);
        $tr->run();

        return $user;
    }

    /**
     * @param User $user
     * @param UserValueObject $valueObject
     * @return User
     */
    public function update(User $user, UserValueObject $valueObject): User
    {
        $user = $user
            ->setEmail($valueObject->getEmail())
            ->setUsername($valueObject->getUsername())
            ->setPassword($valueObject->getPassword())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($user);
        $tr->run();

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        $tr = new Transaction($this->orm);
        $tr->delete($user);
        $tr->run();

        return true;
    }
}
