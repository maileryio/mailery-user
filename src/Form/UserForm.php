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
use Cycle\ORM\Transaction;
use FormManager\Factory as F;
use FormManager\Form;
use Mailery\User\Entity\User;
use Mailery\User\Repository\UserRepository;
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
     * @var User
     */
    private ?User $user;

    /**
     * {@inheritdoc}
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
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
     * @return User
     */
    public function save(): User
    {
        $email = $this['email']->getValue();
        $username = $this['username']->getValue();
        $password = $this['password']->getValue();

        if (($user = $this->user) === null) {
            $user = new User();
        }

        $user
            ->setEmail($email)
            ->setUsername($username)
            ->setPassword((new PasswordHasher)->hash($password))
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($user);
        $tr->run();

        return $user;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->orm->getRepository(User::class);

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
}
