<?php
declare(strict_types=1);

namespace Mailery\User\Console;

use Mailery\User\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\StorageInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class CreateCommand extends Command
{
    /**
     * @var CycleDependencyProxy
     */
    private CycleDependencyProxy $promise;

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
     * @param CycleDependencyProxy $promise
     * @param Manager $manager
     * @param StorageInterface $storage
     */
    public function __construct(CycleDependencyProxy $promise, Manager $manager, StorageInterface $storage)
    {
        $this->promise = $promise;
        $this->manager = $manager;
        $this->storage = $storage;
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a user')
            ->addArgument('login', InputArgument::REQUIRED, 'Login')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('isAdmin', InputArgument::OPTIONAL, 'Create user as admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $isAdmin = (bool) $input->getArgument('isAdmin');

        $user = new User($login, $password);
        try {
            (new EntityWriter($this->promise->getORM()))->write([$user]);

            if ($isAdmin) {
                $this->manager->assign($this->storage->getRoleByName('admin'), $user->getId());
            }

            $io->success('User created');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
