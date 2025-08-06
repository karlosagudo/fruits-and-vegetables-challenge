<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus;

use App\Application\QueryHandlerInterface;
use App\Application\QueryInterface;
use App\Infrastructure\Bus\QueryBus;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * @group Unit
 *
 * @covers \App\Infrastructure\Bus\QueryBus
 *
 * @internal
 */
class QueryBusTest extends TestCase
{
    public function testQueryGoesToCorrectHandle()
    {
        $queryHandler1 = $this->queryHandlerDummy();
        $queryHandler2 = $this->queryHandlerDummy();
        $realHandler = $this->queryHandlerReal();
        $fakeLocator = $this->fakeLocator();
        $fakeLocator->setQueryHandlers($queryHandler1, $queryHandler2, $realHandler);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('info')
            ->with('Query Called:App\Tests\Infrastructure\Bus\realQuery')
        ;
        $sutQueryBus = new QueryBus($fakeLocator, $logger);
        $query = $this->createRealQuery();
        $response = $sutQueryBus->query($query);
        $this->assertEquals(['test' => 'done', 'date' => '2030-01-01T12:00:00+00:00'], $response);
        $this->assertTrue($realHandler->hasBeenCalled);
        $this->assertFalse($queryHandler1->hasBeenCalled);
        $this->assertFalse($queryHandler2->hasBeenCalled);
    }

    public function testNoHandlerRaiseException()
    {
        $queryHandler1 = $this->queryHandlerDummy();
        $queryHandler2 = $this->queryHandlerDummy();
        $queryHandler3 = $this->queryHandlerDummy();
        $fakeLocator = $this->fakeLocator();
        $fakeLocator->setQueryHandlers($queryHandler1, $queryHandler2, $queryHandler3);
        $logger = $this->createMock(LoggerInterface::class);
        $sutQueryBus = new QueryBus($fakeLocator, $logger);
        $query = $this->createRealQuery();
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('QueryHandler doesnt existsApp\Tests\Infrastructure\Bus\realQueryHandler');
        $sutQueryBus->query($query);
        $this->assertFalse($queryHandler1->hasBeenCalled);
        $this->assertFalse($queryHandler2->hasBeenCalled);
        $this->assertFalse($queryHandler3->hasBeenCalled);
    }

    public function testGetSubscribedServices()
    {
        $fakeLocator = $this->fakeLocator();
        $expected = new SubscribedService('handlers', ContainerInterface::class, attributes: new AutowireLocator('application.query.handler'));
        $logger = $this->createMock(LoggerInterface::class);
        $sutQueryBus = new QueryBus($fakeLocator, $logger);
        $this->assertEquals($sutQueryBus::getSubscribedServices(), [$expected]);
    }

    private function createRealQuery(): QueryInterface
    {
        return new realQuery();
    }

    private function queryHandlerDummy(): QueryHandlerInterface
    {
        return new class implements QueryHandlerInterface {
            public bool $hasBeenCalled = false;

            public function handle(QueryInterface $query): array|object
            {
                $this->hasBeenCalled = true;
            }
        };
    }

    private function queryHandlerReal(): QueryHandlerInterface
    {
        return new realQueryHandler();
    }

    private function fakeLocator(): ContainerInterface
    {
        return new class implements ContainerInterface {
            private array $queryHandlers = [];

            public function setQueryHandlers(QueryHandlerInterface ...$queryHandlers): void
            {
                foreach ($queryHandlers as $queryHandler) {
                    $this->queryHandlers[$queryHandler::class] = $queryHandler;
                }
            }

            public function getProvidedServices(): array
            {
                return $this->queryHandlers;
            }

            public function get(string $id)
            {
                return $this->queryHandlers[$id];
            }

            public function has(string $id): bool
            {
                return array_key_exists($id, $this->queryHandlers);
            }
        };
    }
}

class realQueryHandler implements QueryHandlerInterface
{
    public bool $hasBeenCalled = false;

    public function handle(QueryInterface $query): array|object
    {
        $this->hasBeenCalled = true;

        return (object) ['test' => 'done', 'date' => new \DateTimeImmutable('2030-01-01 12:00:00')];
    }
}

class realQuery implements QueryInterface {}
