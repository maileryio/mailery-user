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
     * @var array
     */
    private array $roles;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $timezone;

    /**
     * @param UserForm $form
     * @return self
     */
    public static function fromForm(UserForm $form): self
    {
        $new = new self();
        $new->email = $form->getEmail();
        $new->username = $form->getUsername();
        $new->password = $form->getPassword();
        $new->roles = $form->getRoles();
        $new->status = $form->getStatus();
        $new->timezone = $form->getTimezone();

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
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
