<?php

namespace Mailery\User\Form;

use Mailery\User\Entity\User;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use FormManager\Form;
use FormManager\Factory as F;
use Cycle\ORM\Transaction;
use Cycle\ORM\ORMInterface;
use Yiisoft\Security\PasswordHasher;
use Mailery\User\Repository\UserRepository;

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
     * @inheritdoc
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

        $emailConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) use($userRepo) {
                if (empty($value)) {
                    return;
                }

                $user = $userRepo->findByEmail($value, $this->user);
                if ($user !== null) {
                    $context->buildViolation('This email already exists.')
                        ->atPath('email')
                        ->addViolation();
                }
            }
        ]);

        $usernameConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) use($userRepo) {
                if (empty($value)) {
                    return;
                }

                $user = $userRepo->findByUsername($value, $this->user);
                if ($user !== null) {
                    $context->buildViolation('This username already exists.')
                        ->atPath('username')
                        ->addViolation();
                }
            }
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
            }
        ]);

        return [
            'email' => F::text('Email')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Email())
                ->addConstraint($emailConstraint),
            'username' => F::text('Username')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Regex([
                    'pattern' => '/^[a-zA-Z0-9]+$/i',
                ]))
                ->addConstraint($usernameConstraint),
            'password' => F::password('Password')
                ->addConstraint(new Constraints\NotBlank()),
            'confirmPassword' => F::password('Confirm password')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint($confirmPasswordConstraint),

            '' => F::submit($this->user === null ? 'Create' : 'Update'),
        ];
    }

}