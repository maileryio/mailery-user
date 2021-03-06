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

use FormManager\Factory as F;
use FormManager\Form;
use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Service\UserCrudService;
use Mailery\User\ValueObject\UserValueObject;
use Mailery\User\Enum\Rbac as UserRbac;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\StorageInterface;
use Yiisoft\Rbac\Role;

class UserForm extends Form
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
     * @var UserCrudService
     */
    private UserCrudService $userCrudService;

    /**
     * @var Manager
     */
    private Manager $manager;

    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * @param UserRepository $userRepo
     * @param UserCrudService $userCrudService
     * @param Manager $manager
     * @param StorageInterface $storage
     */
    public function __construct(
        UserRepository $userRepo,
        UserCrudService $userCrudService,
        Manager $manager,
        StorageInterface $storage
    ) {
        $this->userRepo = $userRepo;
        $this->userCrudService = $userCrudService;
        $this->manager = $manager;
        $this->storage = $storage;
        parent::__construct($this->inputs());
    }

    /**
     * @param string $csrf
     * @return \self
     */
    public function withCsrf(string $value, string $name = '_csrf'): self
    {
        $this->offsetSet($name, F::hidden($value));

        return $this;
    }

    /**
     * @param User $user
     * @return self
     */
    public function withUser(User $user): self
    {
        $this->user = $user;
        $this->offsetSet('', F::submit('Update'));

        $this['email']->setValue($user->getEmail());
        $this['username']->setValue($user->getUsername());
//        $this['role']->setValue($user->getEmail());
        $this['status']->setValue($user->getStatus());

        return $this;
    }

    /**
     * @return User|null
     */
    public function save(): ?User
    {
        if (!$this->isValid()) {
            return null;
        }

        $valueObject = UserValueObject::fromForm($this);

        if (($user = $this->user) === null) {
            $user = $this->userCrudService->create($valueObject);
        } else {
            $this->userCrudService->update($user, $valueObject);
        }

        return $user;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        $emailConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) {
                if (empty($value)) {
                    return;
                }

                $user = $this->userRepo->findByEmail($value, $this->user);
                if ($user !== null) {
                    $context->buildViolation('This email already exists.')
                        ->atPath('email')
                        ->addViolation();
                }
            },
        ]);

        $usernameConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) {
                if (empty($value)) {
                    return;
                }

                $user = $this->userRepo->findByUsername($value, $this->user);
                if ($user !== null) {
                    $context->buildViolation('This username already exists.')
                        ->atPath('username')
                        ->addViolation();
                }
            },
        ]);

        $confirmPasswordConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) {
                if (empty($value)) {
                    return;
                }

                if ($value !== $this['password']->getValue()) {
                    $context->buildViolation('Password and confirm password does not match.')
                        ->atPath('confirmPassword')
                        ->addViolation();
                }
            },
        ]);

        $roleOptions = $this->getRoleOptions();
        $statusOptions = $this->getStatusOptions();

        return [
            'email' => F::text('Email')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Email())
                ->addConstraint($emailConstraint),
            'username' => F::text('Username')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Length([
                    'min' => 4,
                ]))
                ->addConstraint(new Constraints\Regex([
                    'pattern' => '/^[a-zA-Z0-9]+$/i',
                ]))
                ->addConstraint($usernameConstraint),
            'password' => F::password('Password')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Length([
                    'min' => 6,
                ])),
            'confirmPassword' => F::password('Confirm password')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint($confirmPasswordConstraint),

            'role' => F::select('Role', $roleOptions)
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Choice([
                    'choices' => array_keys($roleOptions),
                ])),
            'status' => F::select('Status', $statusOptions)
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Choice([
                    'choices' => array_keys($statusOptions),
                ])),

            '' => F::submit($this->user === null ? 'Create' : 'Update'),
        ];
    }

    /**
     * @return array
     */
    public function getRoleOptions(): array
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
    public function getStatusOptions(): array
    {
        return [
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_DISABLED => 'Disabled',
        ];
    }
}
