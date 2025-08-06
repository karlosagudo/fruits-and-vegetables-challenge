<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Domain\Events\DomainEventInterface;
use App\Domain\Events\DomainEventPublisher;
use App\Domain\Models\DomainEventModel;
use App\Domain\Repositories\DomainEventRepositoryInterface;
use App\Infrastructure\Bus\CommandMiddleWares\CommandHandlerSelectorMiddleware;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * @group Unit
 *
 * @covers \App\Domain\Events\DomainEventPublisher
 * @covers \App\Domain\Models\DomainEventModel
 * @covers \App\Infrastructure\Bus\CommandMiddleWares\CommandHandlerSelectorMiddleware
 *
 * @internal
 */
class CommandHandlerSelectorMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        DomainEventPublisher::instance()->reset();
    }

    public function testItGoesToTheCorrectHandler(): void
    {
        $realCommand = $this->getRealCommand();
        $realCommandHandler = new realCommandHandler();
        $fakeLocator = $this->fakeLocator();
        $fakeLocator->setCommandHandlers($realCommandHandler);
        $fakeDispatcher = $this->createMock(EventDispatcherInterface::class);
        $fakeDispatcher->expects($this->never())->method('dispatch');
        $domainEventRepository = $this->createMock(DomainEventRepositoryInterface::class);
        Clock::set(new MockClock());
        $clock = Clock::get();
        $sutCommandHandlerMiddleware = new CommandHandlerSelectorMiddleware($fakeLocator, $fakeDispatcher, $domainEventRepository, $clock);
        ($sutCommandHandlerMiddleware)($realCommand, $this->fakeCallable());
        self::assertTrue($realCommandHandler->hasBeenCalled);
    }

    public function testItRaisesExceptionIfNoCommandHandlerFound(): void
    {
        $fakeCommand = new FakeCommand();
        $fakeLocator = $this->fakeLocator();
        $fakeDispatcher = $this->createMock(EventDispatcherInterface::class);
        $fakeDispatcher->expects($this->never())->method('dispatch');
        $domainEventRepository = $this->createMock(DomainEventRepositoryInterface::class);
        Clock::set(new MockClock());
        $clock = Clock::get();
        $sutCommandHandlerMiddleware = new CommandHandlerSelectorMiddleware($fakeLocator, $fakeDispatcher, $domainEventRepository, $clock);
        self::expectException(\Error::class);
        self::expectExceptionMessage('The command Handler doesnt exists:'.FakeCommandHandler::class);
        ($sutCommandHandlerMiddleware)($fakeCommand, $this->fakeCallable());
    }

    public function testGetSubscribedServices(): void
    {
        $fakeLocator = $this->fakeLocator();
        $expected = new SubscribedService('handlers', ContainerInterface::class, attributes: new AutowireLocator('application.command.handler'));
        $fakeDispatcher = $this->createMock(EventDispatcherInterface::class);
        $fakeDispatcher->expects($this->never())->method('dispatch');
        $domainEventRepository = $this->createMock(DomainEventRepositoryInterface::class);
        Clock::set(new MockClock());
        $clock = Clock::get();
        $sutCommandHandlerMiddleware = new CommandHandlerSelectorMiddleware($fakeLocator, $fakeDispatcher, $domainEventRepository, $clock);
        $this->assertEquals($sutCommandHandlerMiddleware::getSubscribedServices(), [$expected]);
    }

    public function testDomainEventsGetDispatched(): void
    {
        // Given
        $realCommand = $this->getRealCommand();
        $realCommandHandler = new realCommandHandler();

        $fakeLocator = $this->fakeLocator();
        $fakeLocator->setCommandHandlers($realCommandHandler);

        $fakeDomainEvent = $this->fakeDomainEvent();
        DomainEventPublisher::instance()->publish($fakeDomainEvent);

        $fakeDispatcher = $this->createMock(EventDispatcherInterface::class);
        $fakeDispatcher->expects($this->once())->method('dispatch');

        $faker = Factory::create('fr_FR');
        $fakeDate = new \DateTimeImmutable($faker->iso8601());
        $mockClock = new MockClock($fakeDate);
        Clock::set($mockClock);
        $clock = Clock::get();

        $domainEventRepository = $this->createMock(DomainEventRepositoryInterface::class);
        $domainEventRepository->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    static function (DomainEventModel $event) use ($fakeDate, $fakeDomainEvent): bool {
                        return $event->occurredOn === $fakeDate->getTimestamp()
                            && $event->fqnClass === $fakeDomainEvent->getClass()
                            && $event->event->getClass() === $fakeDomainEvent->getClass();
                    }
                )
            )
        ;

        $middleWare = $this->middlewareSpy();
        $sutCommandHandlerMiddleware = new CommandHandlerSelectorMiddleware($fakeLocator, $fakeDispatcher, $domainEventRepository, $clock);
        ($sutCommandHandlerMiddleware)($realCommand, $middleWare);

        self::assertTrue($realCommandHandler->hasBeenCalled);
        self::assertEmpty(DomainEventPublisher::instance()->retrieve());
        self::assertTrue($middleWare->isCalled());
    }

    private function fakeDomainEvent(): DomainEventInterface
    {
        return new class implements DomainEventInterface {
            public function getClass(): string
            {
                return '\App\Tests';
            }
        };
    }

    private function fakeCallable(): \Closure
    {
        return function (CommandInterface $command) {};
    }

    private function getRealCommand(): realCommand
    {
        return new realCommand();
    }

    private function fakeLocator(): ContainerInterface
    {
        return new class implements ContainerInterface {
            private array $commandHandlers = [];

            public function setCommandHandlers(CommandHandlerInterface ...$commandHandlers): void
            {
                foreach ($commandHandlers as $commandHandler) {
                    $this->commandHandlers[$commandHandler::class] = $commandHandler;
                }
            }

            public function getProvidedServices(): array
            {
                return $this->commandHandlers;
            }

            public function get(string $id)
            {
                return $this->commandHandlers[$id];
            }

            public function has(string $id): bool
            {
                return array_key_exists($id, $this->commandHandlers);
            }
        };
    }

    private function middlewareSpy(): callable
    {
        return new class {
            private bool $isCalled = false;

            public function __invoke(CommandInterface $command): void
            {
                $this->isCalled = true;
            }

            public function isCalled(): bool
            {
                return $this->isCalled;
            }
        };
    }
}

class realCommand implements CommandInterface {}

class realCommandHandler implements CommandHandlerInterface
{
    public bool $hasBeenCalled = false;

    public function handle(CommandInterface $command): void
    {
        $this->hasBeenCalled = true;
    }
}
