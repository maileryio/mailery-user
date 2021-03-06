<?php
declare(strict_types=1);

namespace Mailery\User\Console;

use Mailery\User\Entity\User;
use Mailery\User\Form\UserForm;
use Mailery\User\Enum\Rbac;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;
use FormManager\Inputs\Input as FormInput;

class CreateCommand extends Command
{
    /**
     * @var UserForm
     */
    private UserForm $userForm;

    /**
     * @var string
     */
    protected static $defaultName = 'user/create';

    /**
     * @param UserForm $userForm
     */
    public function __construct(UserForm $userForm)
    {
        $this->userForm = $userForm;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        $statuses = array_keys($this->userForm->getStatusOptions());
        $roles = array_keys($this->userForm->getRoleOptions());

        $this
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('status', InputArgument::OPTIONAL, sprintf('Status (%s)', implode(', ', $statuses)))
            ->addArgument('role', InputArgument::OPTIONAL, sprintf('Role (%s)', implode(', ', $roles)));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $status = $input->getArgument('status') ?? User::STATUS_ACTIVE;
        $role = $input->getArgument('role') ?? Rbac::ROLE_ADMIN;

        $this->userForm->setValue([
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'confirmPassword' => $password,
            'status' => $status,
            'role' => $role,
        ]);

        try {
            if (($user = $this->userForm->save()) === null) {
                foreach ($this->userForm as $input) {
                    /** @var FormInput $input */
                    if (($error = $input->getError()) === null) {
                        continue;
                    }
                    throw new \RuntimeException(
                        sprintf(
                            "Failed validation\n - field: %s\n - value: %s\n - error: %s",
                            $input->getAttribute('name'),
                            $input->getValue(),
                            $error
                        )
                    );
                }
            }

            $io->success('User created');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
