<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\CommandInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @property ServiceLocator $container
 */
class MiddlewarePipelineFactory implements ServiceSubscriberInterface
{
    public function __construct(private readonly ContainerInterface $container) {}

    public static function getSubscribedServices(): array
    {
        return [
            new SubscribedService(
                'handlers',
                ContainerInterface::class,
                false,
                new AutowireLocator('infrastructure.command.middleware')
            ),
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(): callable
    {
        $nextMiddleware = static function (CommandInterface $command): void {
            // do nothing
        };
        foreach ($this->container->getProvidedServices() as $providedService) {
            /** @var callable $middleware */
            $middleware = $this->container->get($providedService);
            $nextMiddleware = static function (CommandInterface $command) use ($middleware, $nextMiddleware): void {
                $middleware($command, $nextMiddleware);
            };
        }

        return $nextMiddleware;
    }
}
