<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:promote',
    description: 'Promote or demote a user role',
)]
final class PromoteUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository         $users,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('role',  InputArgument::REQUIRED, 'Role to add (e.g. ROLE_ADMIN)')
            ->addOption('remove', 'r', InputOption::VALUE_NONE, 'Remove the role instead of adding it');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role  = strtoupper($input->getArgument('role'));

        $user = $this->users->findByEmail($email);

        if (null === $user) {
            $io->error(sprintf('User "%s" not found.', $email));
            return Command::FAILURE;
        }

        $roles = $user->getRoles();

        if ($input->getOption('remove')) {
            $roles = array_values(array_filter($roles, fn ($r) => $r !== $role));
            $user->setRoles($roles);
            $this->em->flush();
            $io->success(sprintf('Role "%s" removed from "%s".', $role, $email));
        } else {
            if (in_array($role, $roles, true)) {
                $io->warning(sprintf('User "%s" already has role "%s".', $email, $role));
                return Command::SUCCESS;
            }
            $roles[] = $role;
            $user->setRoles($roles);
            $this->em->flush();
            $io->success(sprintf('Role "%s" granted to "%s".', $role, $email));
        }

        return Command::SUCCESS;
    }
}
