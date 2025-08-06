<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Backup;

interface DbBackupStoreServiceInterface
{
    public function execute(): string;

    public function removeFilesOlderThan(int $days = 30): void;
}
