<?php

namespace Mailery\User\Service;

use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;

class CurrentUserService
{
    /**
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        return $this->getSystemUser();
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    private function getSystemUser(): ?User
    {
        // TODO: return system user
        return $this->userRepo->findOne();
    }
}
