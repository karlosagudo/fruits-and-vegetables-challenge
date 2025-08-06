<?php

declare(strict_types=1);

namespace App\Application;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
