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
use Mailery\User\Repository\UserRepository;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\StorageInterface;

class UserCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @var Manager
     */
    private Manager $manager;

    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * @param ORMInterface $orm
     * @param UserRepository $userRepo
     * @param Manager $manager
     * @param StorageInterface $storage
     */
    public function __construct(
        ORMInterface $orm,
        UserRepository $userRepo,
        Manager $manager,
        StorageInterface $storage
    ) {
        $this->orm = $orm;
        $this->userRepo = $userRepo;
        $this->manager = $manager;
        $this->storage = $storage;
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
            ->setPassword($valueObject->getPassword())
            ->setStatus($valueObject->getStatus())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($user);
        $tr->run();

        $role = $this->storage->getRoleByName($valueObject->getRole());
        $this->manager->assign($role, $user->getId());

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
            ->setStatus($valueObject->getStatus())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($user);
        $tr->run();

        foreach ($this->manager->getRolesByUser($user->getId()) as $userRole) {
            $this->manager->revoke($userRole, $user->getId());
        }

        $role = $this->storage->getRoleByName($valueObject->getRole());
        $this->manager->assign($role, $user->getId());

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
