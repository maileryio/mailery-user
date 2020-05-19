<?php

namespace Mailery\User\Service;

use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;
use Yiisoft\Yii\Web\User\User as WebUser;
use Cycle\ORM\ORMInterface;

class CurrentUserService
{
    /**
     * @var WebUser
     */
    private WebUser $webUser;

    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param WebUser $webUser
     * @param ORMInterface $orm
     */
    public function __construct(WebUser $webUser, ORMInterface $orm)
    {
        $this->webUser = $webUser;
        $this->orm = $orm;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        if (($user = $this->webUser->getIdentity()) !== null) {
            $this->user = $user;
        }

        /* @TODO: temporary hack, move to settings module */
        if (empty($this->user) || !$this->user->getId()) {
            $this->user = $this->getUserRepository($this->orm)->findOne();
        }

        if (!$this->user instanceof User) {
            throw new \RuntimeException('Invalid current user detection');
        }
        return $this->user;
    }

    /**
     * @param User $user
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param ORMInterface $orm
     * @return UserRepository
     */
    private function getUserRepository(ORMInterface $orm): UserRepository
    {
        return $orm->getRepository(User::class);
    }
}