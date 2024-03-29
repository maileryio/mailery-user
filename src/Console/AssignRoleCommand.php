<?php

declare(strict_types=1);

namespace Mailery\User\Console;

use Mailery\User\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\Role;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;

class AssignRoleCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'user/assignRole';

    /**
     * @param CycleDependencyProxy $promise
     * @param Manager $manager
     * @param ItemsStorageInterface $itemsStorage
     */
    public function __construct(
        private CycleDependencyProxy $promise,
        private Manager $manager,
        private ItemsStorageInterface $itemsStorage
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        $this
            ->setDescription('Assign RBAC role to given user')
            ->setHelp('This command allows you to assign RBAC role to user')
            ->addArgument('role', InputArgument::REQUIRED, 'RBAC role')
            ->addArgument('userId', InputArgument::REQUIRED, 'User id');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $roleName = $input->getArgument('role');
        $userId = $input->getArgument('userId');

        try {
            $orm = $this->promise->getORM();
            $userRepo = $orm->getRepository(User::class);
            $user = $userRepo->findByPK($userId);
            if (null === $user) {
                throw new \Exception('Can\'t find user');
            }

            $role = $this->itemsStorage->getRole($roleName);

            if (null === $role) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('Role doesn\'t exist. Create new one? ', false);

                if (!$helper->ask($input, $output, $question)) {
                    return ExitCode::OK;
                }

                $role = new Role($roleName);
                $this->manager->addRole($role);
            }

            $this->manager->assign($role->getName(), $user->getId());

            $io->success('Role was assigned to given user');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}
