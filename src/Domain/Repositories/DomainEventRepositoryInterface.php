<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\DomainEventModel;

interface DomainEventRepositoryInterface
{
    public function save(DomainEventModel $domainEventModel): void;
}
