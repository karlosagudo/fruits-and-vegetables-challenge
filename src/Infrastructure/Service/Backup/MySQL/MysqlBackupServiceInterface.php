<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Backup\MySQL;

interface MysqlBackupServiceInterface
{
    public function execute(): string;
}
