<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Events\DomainEventInterface;

class DomainEventModel implements DomainEventInterface
{
    public function __construct(
        public string $id,
        public int $occurredOn,
        public string $fqnClass,
        public DomainEventInterface $event,
    ) {}

    public function getClass(): string
    {
        return $this->fqnClass;
    }
}
