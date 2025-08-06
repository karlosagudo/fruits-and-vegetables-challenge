<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Backup;

use App\Infrastructure\Service\Backup\MySQL\MysqlBackupServiceInterface;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class DbBackupStoreService implements DbBackupStoreServiceInterface
{
    public function __construct(
        private FilesystemOperator $backupStorage,
        private MysqlBackupServiceInterface $mysqlBackupService,
    ) {}

    public function execute(): string
    {
        $backupTmpPath = $this->mysqlBackupService->execute();

        return $this->storeFile($backupTmpPath);
    }

    public function removeFilesOlderThan(int $days = 30): void
    {
        $files = $this->backupStorage->listContents('/');

        foreach ($files as $file) {
            if ($this->isOlderThan($file, $days)) {
                $this->backupStorage->delete($file->path());
            }
        }
    }

    private function storeFile(string $filePath): string
    {
        // We can't directly store a file in FlySystem from a local file,
        // so content must be read from local file and store it as a file
        // in FlySystem.
        $backupFileName = pathinfo($filePath)['basename'];
        $contents = file_get_contents($filePath);

        try {
            $this->backupStorage->write($backupFileName, false !== $contents ? $contents : '');
        } catch (FileException|FilesystemException|UnableToWriteFile $e) {
            throw new FileException($e->getMessage());
        }

        return $backupFileName;
    }

    private function isOlderThan(StorageAttributes $file, int $days): bool
    {
        $lastModified = $file->lastModified();

        $now = new \DateTimeImmutable();
        $fileDate = new \DateTimeImmutable('@'.$lastModified);

        $diff = $now->diff($fileDate);
        $daysDiff = $diff->format('%a');

        return $daysDiff > $days;
    }
}
