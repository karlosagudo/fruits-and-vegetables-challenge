<?php

declare(strict_types=1);

namespace App\Application;

interface QueryHandlerInterface
{
    /**
     * @return array<mixed>|object
     */
    public function handle(QueryInterface $query): array|object;
}
