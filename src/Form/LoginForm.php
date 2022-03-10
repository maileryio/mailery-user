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

use Mailery\User\Repository\UserRepository;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Result;

class LoginForm extends FormModel
{

    /**
     * @var string|null
     */
    private ?string $login = null;

    /**
     * @var string|null
     */
    private ?string $password = null;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;

        parent::__construct();
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'login' => 'Login',
            'password' => 'Password',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'login' => [
                Required::rule(),
                HasLength::rule()->min(4)->max(255),
            ],
            'password' => [
                Required::rule(),
                HasLength::rule()->min(6)->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();

                    if ($this->getIdentity() === null) {
                        $result->addError('Invalid login or password.');
                    }

                    return $result;
                }),
            ],
        ];
    }

    /**
     * @return IdentityInterface|null
     */
    public function getIdentity(): ?IdentityInterface
    {
        $identity = $this->userRepo->findByLogin($this->login);
        if ($identity !== null && $identity->validatePassword($this->password)) {
            return $identity;
        }

        return null;
    }

}
