<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Domain\Events\DomainEventInterface;
use App\Domain\Events\DomainEventPublisher;
use App\Domain\Models\DomainEventModel;
use App\Domain\Repositories\DomainEventRepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class CommandHandlerSelectorMiddleware implements CommandMiddlewareInterface, ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
        private EventDispatcherInterface $eventDispatcher,
        private DomainEventRepositoryInterface $domainEventRepository,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(CommandInterface $command, callable $nextMiddleware): void
    {
        $commandHandlerClass = $command::class.'Handler';
        if (!$this->container->has($commandHandlerClass)) {
            throw new \Error('The command Handler doesnt exists:'.$commandHandlerClass);
        }

        /** @var CommandHandlerInterface $handler */
        $handler = $this->container->get($commandHandlerClass);
        $handler->handle($command);

        $publisher = DomainEventPublisher::instance();
        $domainEvents = $publisher->retrieve();
        foreach ($domainEvents as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
            $this->persistEvent($domainEvent);
        }

        $publisher->reset();

        $nextMiddleware($command);
    }

    public static function getSubscribedServices(): array
    {
        return [
            new SubscribedService(
                'handlers',
                ContainerInterface::class,
                false,
                new AutowireLocator('application.command.handler')
            ),
        ];
    }

    private function persistEvent(DomainEventInterface $domainEvent): void
    {
        $domainEvent = new DomainEventModel(
            Uuid::v7()->toRfc4122(),
            $this->clock->now()->getTimestamp(),
            $domainEvent->getClass(),
            $domainEvent
        );
        $this->domainEventRepository->save(
            $domainEvent
        );
    }
}
