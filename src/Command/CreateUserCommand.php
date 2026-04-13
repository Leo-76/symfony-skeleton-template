<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user from the command line',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ValidatorInterface          $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email',    InputArgument::REQUIRED, 'User email')
            ->addArgument('fullname', InputArgument::REQUIRED, 'Full name')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password')
            ->addOption('admin',    null, InputOption::VALUE_NONE, 'Grant ROLE_ADMIN')
            ->addOption('verified', null, InputOption::VALUE_NONE, 'Mark email as verified');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email    = $input->getArgument('email');
        $fullname = $input->getArgument('fullname');
        $password = $input->getArgument('password');
        $isAdmin  = $input->getOption('admin');

        $user = new User();
        $user->setEmail($email);
        $user->setFullName($fullname);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setIsVerified($input->getOption('verified'));

        if ($isAdmin) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getPropertyPath() . ': ' . $error->getMessage());
            }
            return Command::FAILURE;
        }

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf(
            'User "%s" (%s) created successfully.%s',
            $user->getFullName(),
            $user->getEmail(),
            $isAdmin ? ' [ADMIN]' : '',
        ));

        return Command::SUCCESS;
    }
}
