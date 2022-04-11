<?php

namespace Mailery\User\Service;

use Mailery\User\Entity\User;

class CurrentUserService
{
    /**
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
