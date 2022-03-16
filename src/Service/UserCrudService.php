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
use Mailery\User\Entity\User;
use Mailery\User\ValueObject\UserValueObject;
use Yiisoft\Rbac\Manager;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class UserCrudService
{
    /**
     * @param ORMInterface $orm
     * @param Manager $manager
     */
    public function __construct(
        private ORMInterface $orm,
        private Manager $manager
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
        ;

        (new EntityWriter($this->orm))->write([$user]);

        $this->manager->assign($valueObject->getRole(), $user->getId());

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

        (new EntityWriter($this->orm))->write([$user]);

        foreach ($this->manager->getRolesByUserId($user->getId()) as $role) {
            $this->manager->revoke($role->getName(), $user->getId());
        }

        $this->manager->assign($valueObject->getRole(), $user->getId());

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        (new EntityWriter($this->orm))->delete([$user]);

        return true;
    }
}
