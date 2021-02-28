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
use Mailery\User\Repository\UserRepository;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\User\CurrentUser;

class LoginForm extends Form
{
    /**
     * @var CurrentUser
     */
    private CurrentUser $user;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @param CurrentUser $user
     * @param UserRepository $userRepo
     */
    public function __construct(
        CurrentUser $user,
        UserRepository $userRepo
    ) {
        $this->user = $user;
        $this->userRepo = $userRepo;
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
     * @return IdentityInterface|null
     */
    public function login(): ?IdentityInterface
    {
        if (!$this->isValid()) {
            return null;
        }

        $login = $this['login']->getValue();
        $password = $this['password']->getValue();

        $identity = $this->userRepo->findByLogin($login);

        if ($identity === null || !$identity->validatePassword($password)) {
            throw new \InvalidArgumentException('Invalid login or password');
        }

        if (!$this->user->login($identity)) {
            throw new \InvalidArgumentException('Unable to login');
        }

        return $identity;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        $passwordConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) {
                if (empty($value)) {
                    return;
                }

                $login = $this['login']->getValue();
                $password = $this['password']->getValue();

                $user = $this->userRepo->findByLogin($login);
                if ($user === null || !$user->validatePassword($password)) {
                    $context->buildViolation('Invalid login or password.')
                        ->atPath('password')
                        ->addViolation();
                }
            },
        ]);

        return [
            'login' => F::text('Login')
                ->addConstraint(new Constraints\NotBlank()),
            'password' => F::password('Password')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint($passwordConstraint),

            '' => F::submit('Submit'),
        ];
    }
}
