<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;

class FakeCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void {}
}
