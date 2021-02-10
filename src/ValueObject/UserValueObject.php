<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\ValueObject;

use Mailery\User\Form\UserForm;

class UserValueObject
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string
     */
    private string $role;

    /**
     * @var string
     */
    private string $status;

    /**
     * @param UserForm $form
     * @return self
     */
    public static function fromForm(UserForm $form): self
    {
        $new = new self();

        $new->email = $form['email']->getValue();
        $new->username = $form['username']->getValue();
        $new->password = $form['password']->getValue();
        $new->role = $form['role']->getValue();
        $new->status = $form['status']->getValue();

        return $new;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $email
     * @return self
     */
    public function withEmail(string $email): self
    {
        $new = clone $this;
        $new->email = $email;

        return $new;
    }

    /**
     * @param string $username
     * @return self
     */
    public function withUsername(string $username): self
    {
        $new = clone $this;
        $new->username = $username;

        return $new;
    }

    /**
     * @param string $password
     * @return self
     */
    public function withPassword(string $password): self
    {
        $new = clone $this;
        $new->password = $password;

        return $new;
    }

    /**
     * @param string $role
     * @return self
     */
    public function withRole(string $role): self
    {
        $new = clone $this;
        $new->role = $role;

        return $new;
    }

    /**
     * @param string $status
     * @return self
     */
    public function withStatus(string $status): self
    {
        $new = clone $this;
        $new->status = $status;

        return $new;
    }
}
