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

use Cycle\ORM\EntityManagerInterface;
use Mailery\User\Entity\User;
use Mailery\User\ValueObject\UserValueObject;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class UserCrudService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param Manager $manager
     * @param ItemsStorageInterface $itemsStorage
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Manager $manager,
        private ItemsStorageInterface $itemsStorage
    ) {}

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
            ->setCountry($valueObject->getCountry())
            ->setTimezone($valueObject->getTimezone())
        ;

        (new EntityWriter($this->entityManager))->write([$user]);

        foreach ($valueObject->getRoles() as $roleName) {
            if (($role = $this->itemsStorage->getRole($roleName)) !== null) {
                $this->manager->assign($role->getName(), $user->getId());
            }
        }

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
            ->setCountry($valueObject->getCountry())
            ->setTimezone($valueObject->getTimezone())
        ;

        (new EntityWriter($this->entityManager))->write([$user]);

        foreach ($this->manager->getRolesByUserId($user->getId()) as $role) {
            $this->manager->revoke($role, $user->getId());
        }

        foreach ($valueObject->getRoles() as $roleName) {
            if (($role = $this->itemsStorage->getRole($roleName)) !== null) {
                $this->manager->assign($role->getName(), $user->getId());
            }
        }

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        (new EntityWriter($this->entityManager))->delete([$user]);

        return true;
    }
}
