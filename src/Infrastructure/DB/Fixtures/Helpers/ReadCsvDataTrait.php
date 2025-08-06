<?php

declare(strict_types=1);

namespace App\Infrastructure\DB\Fixtures\Helpers;

trait ReadCsvDataTrait
{
    private const DATA_DIR = 'data';

    public function importData(string $name): array
    {
        $curPath = __DIR__.'/../'.self::DATA_DIR;

        return $this->importCsv($curPath.'/'.$name.'.csv');
    }

    private function importAllCsv(): array
    {
        $data = [];
        $curPath = __DIR__.'/'.self::DATA_DIR;

        $files = scandir($curPath);
        $csvFiles = array_filter($files, function ($file) {
            return 'csv' === pathinfo($file, PATHINFO_EXTENSION);
        });

        foreach ($csvFiles as $csvFile) {
            $csvData = $this->importCsv($curPath.'/'.$csvFile);
            $data[pathinfo($csvFile, PATHINFO_FILENAME)] = $csvData;
        }

        return $data;
    }

    private function importCsv(string $filePath): array
    {
        $result = [];

        $data = file($filePath);

        foreach ($data as $line) {
            $result[] = explode(';', trim($line));
        }

        return $result;
    }
}
