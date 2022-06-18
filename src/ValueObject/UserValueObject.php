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
use Mailery\User\Field\UserStatus;
use Mailery\Common\Field\Country;
use Mailery\Common\Field\Timezone;

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
     * @var UserStatus
     */
    private UserStatus $status;

    /**
     * @var Country
     */
    private Country $country;

    /**
     * @var Timezone
     */
    private Timezone $timezone;

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
        $new->country = $form->getCountry();

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
     * @return UserStatus
     */
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @return Timezone
     */
    public function getTimezone(): Timezone
    {
        return $this->timezone;
    }
}
