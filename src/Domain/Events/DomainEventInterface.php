<?php

declare(strict_types=1);

namespace App\Domain\Events;

interface DomainEventInterface
{
    public function getClass(): string;
}
