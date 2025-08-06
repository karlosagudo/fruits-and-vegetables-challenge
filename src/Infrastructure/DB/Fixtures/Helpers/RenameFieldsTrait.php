<?php

declare(strict_types=1);

namespace App\Infrastructure\DB\Fixtures\Helpers;

trait RenameFieldsTrait
{
    protected function renameKeys(array $row, array $fields): array
    {
        $newRow = [];
        foreach ($fields as $i => $field) {
            $newRow[$field] = $row[$i];
        }

        return $newRow;
    }
}
