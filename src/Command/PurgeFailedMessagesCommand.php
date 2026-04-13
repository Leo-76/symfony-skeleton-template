<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:messenger:purge-failed',
    description: 'Purge failed Messenger messages older than N days',
)]
final class PurgeFailedMessagesCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'days',
            'd',
            InputOption::VALUE_REQUIRED,
            'Delete messages older than this many days',
            30,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        $date = (new \DateTimeImmutable())->modify(sprintf('-%d days', $days));

        $deleted = $this->connection->executeStatement(
            'DELETE FROM messenger_messages WHERE queue_name = :queue AND created_at < :date',
            [
                'queue' => 'failed',
                'date'  => $date->format('Y-m-d H:i:s'),
            ],
        );

        $io->success(sprintf('%d failed message(s) older than %d days purged.', $deleted, $days));

        return Command::SUCCESS;
    }
}
