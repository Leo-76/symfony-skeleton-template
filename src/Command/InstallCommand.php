<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:install',
    description: 'First-run wizard: create database, run migrations, create admin user',
)]
final class InstallCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🎼 Symfony Skeleton — Installation Wizard');

        // 1. Create database
        $io->section('Step 1/4 — Create database');
        $this->runCommand('doctrine:database:create', ['--if-not-exists' => true], $output);
        $io->success('Database ready.');

        // 2. Run migrations
        $io->section('Step 2/4 — Run migrations');
        $this->runCommand('doctrine:migrations:migrate', ['--no-interaction' => true], $output);
        $io->success('Migrations applied.');

        // 3. Warm up cache
        $io->section('Step 3/4 — Warm up cache');
        $this->runCommand('cache:warmup', [], $output);
        $io->success('Cache warmed up.');

        // 4. Create admin user
        $io->section('Step 4/4 — Create admin user');
        $helper   = $this->getHelper('question');
        $email    = $helper->ask($input, $output, new Question('Admin email: '));
        $fullname = $helper->ask($input, $output, new Question('Full name: '));
        $passQ    = new Question('Password (hidden): ');
        $passQ->setHidden(true);
        $passQ->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passQ);

        $this->runCommand('app:user:create', [
            'email'    => $email,
            'fullname' => $fullname,
            'password' => $password,
            '--admin'  => true,
            '--verified' => true,
        ], $output);

        $io->success([
            'Installation complete!',
            sprintf('Visit http://localhost:8080 and log in as %s', $email),
        ]);

        return Command::SUCCESS;
    }

    private function runCommand(string $name, array $args, OutputInterface $output): void
    {
        $command = $this->getApplication()->find($name);
        $command->run(new ArrayInput($args + ['command' => $name]), $output);
    }
}
