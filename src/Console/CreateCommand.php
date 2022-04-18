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
use Yiisoft\Validator\ValidatorInterface;
use Mailery\User\Service\UserCrudService;
use Mailery\User\ValueObject\UserValueObject;

class CreateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'user/create';

    /**
     * @param UserForm $form
     * @param ValidatorInterface $validator
     * @param UserCrudService $userCrudService
     */
    public function __construct(
        private UserForm $form,
        private ValidatorInterface $validator,
        private UserCrudService $userCrudService
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        $statuses = array_keys($this->form->getStatusListOptions());
        $roles = array_keys($this->form->getRoleListOptions());

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

        $data = [
            'email' => $input->getArgument('email'),
            'username' => $input->getArgument('username'),
            'password' => $input->getArgument('password'),
            'confirmPassword' => $input->getArgument('password'),
            'status' => $input->getArgument('status') ?? User::STATUS_ACTIVE,
            'role' => $input->getArgument('role') ?? Rbac::ROLE_ADMIN,
        ];

        try {
            if ($this->form->load($data, '') && $this->validator->validate($this->form)->isValid()) {
                $valueObject = UserValueObject::fromForm($this->form);
                $user = $this->userCrudService->create($valueObject);
            } else {
                foreach ($this->form->getErrors() as $attribute => $error) {
                    throw new \RuntimeException(
                        sprintf(
                            "Failed validation\n - field: %s\n - value: %s\n - error: %s",
                            $attribute,
                            $this->form->getAttributeValue($attribute),
                            $this->form->getFirstError($attribute)
                        )
                    );
                }
            }

            $io->success(sprintf('User created with ID: %d', $user->getId()));
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
