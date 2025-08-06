<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandInterface;

interface CommandMiddlewareInterface
{
    public function __invoke(CommandInterface $command, callable $nextMiddleware): void;
}
