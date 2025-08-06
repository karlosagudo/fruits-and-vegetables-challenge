<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandInterface;
use App\Infrastructure\Bus\CommandMiddleWares\LogCommandMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @group Unit
 *
 * @covers \App\Infrastructure\Bus\CommandMiddleWares\LogCommandMiddleware
 *
 * @internal
 */
class LogCommandMiddlewareTest extends TestCase
{
    public function testCommandIsLogged(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Command called: '.FakeCommand::class)
        ;

        $fakeCommand = new FakeCommand();
        $middleWare = $this->middlewareSpy();

        $sutMiddleware = new LogCommandMiddleware($loggerMock);
        $sutMiddleware->__invoke($fakeCommand, $middleWare);
        $this->assertTrue($middleWare->isCalled());
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
