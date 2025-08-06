<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Backup\MySQL;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\EnvParameterException;

final class MysqlBackupService implements MysqlBackupServiceInterface
{
    private string $databaseUrl = '';

    public function __construct(
        private LoggerInterface $logger,
        string $databaseUrl
    ) {
        $this->databaseUrl = $databaseUrl;
    }

    public function execute(): string
    {
        return $this->doBackup();
    }

    private function doBackup(): string
    {
        $databaseConfig = $this->getDatabaseConfig();
        $backupTmpPath = $this->getTmpBackupPath();

        $command = sprintf(
            '/usr/bin/mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            $databaseConfig['host'],
            $databaseConfig['port'],
            $databaseConfig['user'],
            $databaseConfig['pass'],
            ltrim($databaseConfig['path'], '/'),
            $backupTmpPath
        );

        $this->executeProcess($command);

        return $backupTmpPath;
    }

    /**
     * @return array<string,mixed>
     */
    private function getDatabaseConfig(): array
    {
        $databaseUrlPath = parse_url($this->databaseUrl);

        if (!$databaseUrlPath) {
            throw new EnvParameterException(['databaseUrl'], null, ' not found in environment.');
        }

        return $databaseUrlPath;
    }

    private function getTmpBackupPath(): string
    {
        $backupFileName = date('Y-m-d_H-i-s').'_backup.sql';

        return sys_get_temp_dir().'/'.$backupFileName;
    }

    /**
     * @throws \ErrorException
     */
    private function executeProcess(string $command): void
    {
        $result = shell_exec('('.$command.') 2>&1');

        // TODO this should be the opposite shouldn't it?
        if (null !== $result && false !== $result) {
            $this->logger->error($result);

            throw new \ErrorException('Unable to execute backup shell.');
        }
    }
}
