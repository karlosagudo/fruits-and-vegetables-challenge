<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Infrastructure\Service\Backup\DbBackupStoreServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:backup-db',
    description: 'Create and store a database backup and remove older backup files.',
)]
class BackupDbCommand extends Command
{
    public function __construct(
        private DbBackupStoreServiceInterface $dbBackupStoreService,
        private int $backupOlderLimit,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $this->dbBackupStoreService->execute();
        $this->dbBackupStoreService->removeFilesOlderThan($this->backupOlderLimit);

        $inputOutput->success('Backup executed and old files removed.');

        return Command::SUCCESS;
    }
}
