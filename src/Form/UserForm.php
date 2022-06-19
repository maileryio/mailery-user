<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Form;

use Mailery\Common\Model\Countries;
use Mailery\Common\Model\Timezones;
use Mailery\User\Entity\User;
use Mailery\User\Field\UserStatus;
use Mailery\Common\Field\Country;
use Mailery\Common\Field\Timezone;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Setting\UserSettingGroup;
use Yiisoft\Rbac\StorageInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\Role;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\MatchRegularExpression;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleSet;

class UserForm extends FormModel implements \Yiisoft\Form\FormModelInterface
{

    /**
     * @var string|null
     */
    private ?string $email = null;

    /**
     * @var string|null
     */
    private ?string $username = null;

    /**
     * @var string|null
     */
    private ?string $password = null;

    /**
     * @var string|null
     */
    private ?string $confirmPassword = null;

    /**
     * @var array
     */
    private array $roles =[];

    /**
     * @var string|null
     */
    private ?string $status = null;

    /**
     * @var string|null
     */
    private ?string $country = null;

    /**
     * @var string|null
     */
    private ?string $timezone = null;

    /**
     * @var User|null
     */
    private ?User $entity = null;

    /**
     * @param UserRepository $userRepo
     * @param Manager $manager
     * @param StorageInterface $storage
     * @param UserSettingGroup $settings
     */
    public function __construct(
        private UserRepository $userRepo,
        private Manager $manager,
        private StorageInterface $storage,
        private UserSettingGroup $settings
    ) {
        $this->country = $this->settings->getDefaultCountry()->getValue();
        $this->timezone = $this->settings->getDefaultTimezone()->getValue();

        parent::__construct();
    }

    /**
     * @param User $entity
     * @return self
     */
    public function withEntity(User $entity): self
    {
        $new = clone $this;
        $new->entity = $entity;
        $new->email = $entity->getEmail();
        $new->username = $entity->getUsername();
        $new->status = $entity->getStatus()->getValue();
        $new->country = $entity->getCountry()->getValue();
        $new->timezone = $entity->getTimezone()->getValue();
        $new->roles = array_map(
            fn (Role $role) => $role->getName(),
            $this->manager->getRolesByUser($entity->getId())
        );

        return $new;
    }

    /**
     * @return bool
     */
    public function hasEntity(): bool
    {
        return $this->entity !== null;
    }

    /**
     * @inheritdoc
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $scope = $formName ?? $this->getFormName();

        if (isset($data[$scope]['roles'])) {
            $data[$scope]['roles'] = array_filter((array) $data[$scope]['roles']);
        }

        return parent::load($data, $formName);
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
        return UserStatus::typecast($this->status);
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return Country::typecast($this->country);
    }

    /**
     * @return Timezone
     */
    public function getTimezone(): Timezone
    {
        return Timezone::typecast($this->timezone);
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'confirmPassword' => 'Confirm password',
            'roles' => 'Roles',
            'status' => 'Status',
            'country' => 'Country',
            'timezone' => 'Timezone',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'email' => [
                Required::rule(),
                Email::rule(),
                HasLength::rule()->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();
                    $record = $this->userRepo->findByEmail($value, $this->entity);

                    if ($record !== null) {
                        $result->addError('This email already exists.');
                    }

                    return $result;
                }),
            ],
            'username' => [
                Required::rule(),
                HasLength::rule()->min(4)->max(255),
                MatchRegularExpression::rule('/^[a-zA-Z0-9]+$/i'),
                Callback::rule(function ($value) {
                    $result = new Result();
                    $record = $this->userRepo->findByUsername($value, $this->entity);

                    if ($record !== null) {
                        $result->addError('This username already exists.');
                    }

                    return $result;
                }),
            ],
            'password' => [
                Required::rule(),
                HasLength::rule()->min(6)->max(255),
            ],
            'confirmPassword' => [
                Required::rule(),
                HasLength::rule()->min(6)->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();

                    if ($value !== $this->password) {
                        $result->addError('Password and confirm password does not match.');
                    }

                    return $result;
                }),
            ],
            'roles' => [
                Required::rule(),
                Each::rule(new RuleSet([
                    InRange::rule(array_keys($this->getRoleListOptions())),
                ]))->message('{error}'),
            ],
            'status' => [
                Required::rule(),
                InRange::rule(array_keys($this->getStatusListOptions())),
            ],
            'country' => [
                Required::rule(),
                InRange::rule(array_keys($this->getCountryListOptions())),
            ],
            'timezone' => [
                Required::rule(),
                InRange::rule(array_keys($this->getTimezoneListOptions())),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getRoleListOptions(): array
    {
        $roles = [];
        foreach ($this->storage->getRoles() as $role) {
            /** @var Role $role */
            $roles[$role->getName()] = $role->getName();
        }

        return $roles;
    }

    /**
     * @return array
     */
    public function getStatusListOptions(): array
    {
        $active = UserStatus::asActive();
        $disabled = UserStatus::asDisabled();

        return [
            $active->getValue() => $active->getLabel(),
            $disabled->getValue() => $disabled->getLabel(),
        ];
    }

    /**
     * @return array
     */
    public function getCountryListOptions(): array
    {
        return (new Countries())->getAll();
    }

    /**
     * @return array
     */
    public function getTimezoneListOptions(): array
    {
        return (new Timezones())
            ->withOffset(true)
            ->withNearestBy($this->country)
            ->getAll();
    }

}
