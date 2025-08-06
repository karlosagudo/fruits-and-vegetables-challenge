<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\CommandInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CompleteCommandBus implements CommandBusInterface
{
    public function __construct(private readonly MiddlewarePipelineFactory $middlewarePipelineFactory) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(CommandInterface $command): void
    {
        $this->middlewarePipelineFactory->create()($command);
    }
}
