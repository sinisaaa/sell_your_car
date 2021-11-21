<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Helper\EmailValidator;
use App\Helper\ValueObjects\RoleCode;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * Command that creates initial admin user.
 */
class CreateAdminCommand extends Command
{

    /**
     * CreateAdminCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param UserService $userService
     */
    public function __construct(private EntityManagerInterface $em, private UserService $userService)
    {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:create-admin')
            ->addArgument('email', InputArgument::REQUIRED, 'Admin Email?')
            ->addArgument('name', InputArgument::REQUIRED, 'Admin Full Name?')
            ->addArgument('password', InputArgument::REQUIRED, 'Password?')
            ->setDescription('Creates a admin account.')
            ->setHelp('This command allows you to create a admin account.')
            ->setHidden(true);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!is_string($email) || !is_string($name) || !is_string($password)) {
            throw new RuntimeException('Invalid parameters.');
        }

        if (false === EmailValidator::isValidEmail($email)) {
            throw new RuntimeException('Invalid email format.');
        }

        $existingUser = $this->em->getRepository(User::class)->findOneBy([
            'username' => $email
        ]);

        if ($existingUser) {
            throw new RuntimeException('Account with same email already exists.');
        }

        /** @var Role $adminRole */
        $adminRole = $this->em->getRepository(Role::class)->findOneBy(['code' => RoleCode::ADMIN]);

        $user = $this->userService->createUser(
            $email,
            $name,
            $password,
            $adminRole
        );

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<fg=green>Admin successfully generated!</>');

        return Command::SUCCESS;
    }
}
