<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandInterface;
use Psr\Log\LoggerInterface;

class LogCommandMiddleware implements CommandMiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
        // empty on purpose
    }

    public function __invoke(CommandInterface $command, callable $nextMiddleware): void
    {
        $this->logger->info('Command called: '.$command::class);
        $nextMiddleware($command);
    }
}
