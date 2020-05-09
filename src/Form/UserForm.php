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

use Cycle\ORM\ORMInterface;
use FormManager\Factory as F;
use FormManager\Form;
use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Service\UserService;
use Mailery\User\ValueObject\UserValueObject;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Yiisoft\Security\PasswordHasher;

class UserForm extends Form
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var User|null
     */
    private ?User $user;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param UserService $userService
     * @param ORMInterface $orm
     */
    public function __construct(UserService $userService, ORMInterface $orm)
    {
        $this->orm = $orm;
        $this->userService = $userService;
        parent::__construct($this->inputs());
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

        $email = $this['email']->getValue();
        $username = $this['username']->getValue();
        $password = $this['password']->getValue();

        $valueObject = UserValueObject::fromForm($this)
            ->withEmail($email)
            ->withUsername($username)
            ->withPassword((new PasswordHasher)->hash($password));

        if (($user = $this->user) === null) {
            $user = $this->userService->create($valueObject);
        } else {
            $this->userService->update($user, $valueObject);
        }

        return $user;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        $userRepo = $this->getUserRepository($this->orm);

        $statusOptions = $this->getStatusOptions();

        $emailConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) use ($userRepo) {
                if (empty($value)) {
                    return;
                }

                $user = $userRepo->findByEmail($value, $this->user);
                if ($user !== null) {
                    $context->buildViolation('This email already exists.')
                        ->atPath('email')
                        ->addViolation();
                }
            },
        ]);

        $usernameConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) use ($userRepo) {
                if (empty($value)) {
                    return;
                }

                $user = $userRepo->findByUsername($value, $this->user);
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

        return [
            'status' => F::select('Status', $statusOptions)
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Choice([
                    'choices' => array_keys($statusOptions),
                ])),
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

            '' => F::submit($this->user === null ? 'Create' : 'Update'),
        ];
    }

    /**
     * @return array
     */
    private function getStatusOptions(): array
    {
        return [
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_DISABLED => 'Disabled',
        ];
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
