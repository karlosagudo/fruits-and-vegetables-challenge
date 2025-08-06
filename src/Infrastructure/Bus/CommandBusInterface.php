<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\CommandInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): void;
}
