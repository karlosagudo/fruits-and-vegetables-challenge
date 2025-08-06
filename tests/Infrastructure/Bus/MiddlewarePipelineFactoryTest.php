<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus;

use App\Application\CommandInterface;
use App\Infrastructure\Bus\CommandMiddleWares\CommandMiddlewareInterface;
use App\Infrastructure\Bus\MiddlewarePipelineFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * @group Unit
 *
 * @covers \App\Infrastructure\Bus\MiddlewarePipelineFactory
 *
 * @internal
 */
class MiddlewarePipelineFactoryTest extends TestCase
{
    public function testMiddlewaresBeingCalled()
    {
        $middleware1 = $this->middlewareSpy();
        $middleware2 = $this->middlewareSpy();
        $middleware3 = $this->middlewareSpy();
        $locatorDummy = $this->fakeLocator();
        $locatorDummy->setMiddlewares($middleware1, $middleware2, $middleware3);
        $sutPipeline = new MiddlewarePipelineFactory($locatorDummy);
        $pipeline = $sutPipeline->create();
        $pipeline($this->commandDummy());
        $this->assertTrue($middleware1->isCalled());
    }

    public function testGetSubscribedServices()
    {
        $middleware1 = $this->middlewareSpy();
        $middleware2 = $this->middlewareSpy();
        $middleware3 = $this->middlewareSpy();
        $locatorDummy = $this->fakeLocator();
        $locatorDummy->setMiddlewares($middleware1, $middleware2, $middleware3);
        $sutPipeline = new MiddlewarePipelineFactory($locatorDummy);
        $expected = new SubscribedService('handlers', ContainerInterface::class, attributes: new AutowireLocator('infrastructure.command.middleware'));
        $this->assertEquals($sutPipeline::getSubscribedServices(), [$expected]);
    }

    private function middlewareSpy(): CommandMiddlewareInterface
    {
        return new class implements CommandMiddlewareInterface {
            private bool $isCalled = false;

            public function __invoke(CommandInterface $command, callable $nextMiddleware): void
            {
                $this->isCalled = true;

                $nextMiddleware($command);
            }

            public function isCalled(): bool
            {
                return $this->isCalled;
            }
        };
    }

    private function fakeLocator(): ContainerInterface
    {
        return new class implements ContainerInterface {
            private array $middlewares = [];

            public function setMiddlewares(CommandMiddlewareInterface ...$middlewares): void
            {
                foreach ($middlewares as $middleware) {
                    $this->middlewares[] = $middleware;
                }
            }

            public function getProvidedServices(): array
            {
                return array_keys($this->middlewares);
            }

            public function get(int|string $id)
            {
                return $this->middlewares[(int) $id];
            }

            public function has(string $id): bool
            {
                return in_array($id, $this->middlewares);
            }
        };
    }

    private function commandDummy(): CommandInterface
    {
        return new class implements CommandInterface {};
    }
}
