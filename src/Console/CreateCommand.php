<?php
declare(strict_types=1);

namespace Mailery\User\Console;

use Mailery\User\Entity\User;
use Mailery\User\Form\UserForm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\StorageInterface;
use Yiisoft\Yii\Console\ExitCode;
use FormManager\Inputs\Input as FormInput;

class CreateCommand extends Command
{
    /**
     * @var UserForm
     */
    private UserForm $userForm;

    /**
     * @var Manager
     */
    private Manager $manager;

    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * @var string
     */
    protected static $defaultName = 'user/create';

    /**
     * @param UserForm $userForm
     * @param Manager $manager
     * @param StorageInterface $storage
     */
    public function __construct(UserForm $userForm, Manager $manager, StorageInterface $storage)
    {
        $this->userForm = $userForm;
        $this->manager = $manager;
        $this->storage = $storage;
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('status', InputArgument::OPTIONAL, 'Status');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $status = $input->getArgument('status') ?? User::STATUS_ACTIVE;

        $this->userForm->setValue([
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'confirmPassword' => $password,
            'status' => $status,
        ]);

        try {
            if (($user = $this->userForm->save()) === null) {
                foreach ($this->userForm as $input) {
                    /** @var $input FormInput */
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

//            if ($isAdmin) {
//                $this->manager->assign($this->storage->getRoleByName('admin'), $user->getId());
//            }

            $io->success('User created');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
