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

use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;
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
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @param UserRepository $userRepo
     * @param Manager $manager
     * @param StorageInterface $storage
     */
    public function __construct(
        private UserRepository $userRepo,
        private Manager $manager,
        private StorageInterface $storage
    ) {
        parent::__construct();
    }

    /**
     * @param User $user
     * @return self
     */
    public function withEntity(User $user): self
    {
        $new = clone $this;
        $new->user = $user;
        $new->email = $user->getEmail();
        $new->username = $user->getUsername();
        $new->status = $user->getStatus();
        $new->roles = array_map(
            fn (Role $role) => $role->getName(),
            $this->manager->getRolesByUser($user->getId())
        );

        return $new;
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
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
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
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
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
                    $record = $this->userRepo->findByEmail($value, $this->user);

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
                    $record = $this->userRepo->findByUsername($value, $this->user);

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
        return [
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_DISABLED => 'Disabled',
        ];
    }

}
