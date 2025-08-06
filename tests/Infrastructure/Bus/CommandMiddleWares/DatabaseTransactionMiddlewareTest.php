<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandInterface;
use App\Infrastructure\Bus\CommandMiddleWares\DatabaseTransactionMiddleware;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Infrastructure\Bus\CommandMiddleWares\DatabaseTransactionMiddleware
 *
 * @internal
 */
class DatabaseTransactionMiddlewareTest extends TestCase
{
    public function testNextCallableIsCalledAndWeCreateATransaction()
    {
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturn($connectionMock)
        ;

        $connectionMock->expects($this->once())
            ->method('commit')
        ;

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $entityManagerMock->method('getConnection')
            ->willReturn($connectionMock)
        ;

        $fakeCommand = $this->fakeCommand();
        $middleWare = $this->middlewareSpy();

        $sutMiddleware = new DatabaseTransactionMiddleware($entityManagerMock);
        $sutMiddleware->__invoke($fakeCommand, $middleWare);
        $this->assertTrue($middleWare->isCalled());
    }

    public function testExceptionWillRollBack()
    {
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturn($connectionMock)
        ;

        $connectionMock->expects($this->once())
            ->method('rollBack')
        ;

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $entityManagerMock->method('getConnection')
            ->willReturn($connectionMock)
        ;

        $fakeCommand = $this->fakeCommand();
        $middleWare = $this->middlewareWithExceptionSpy();
        $this->expectException(\Error::class);

        $sutMiddleware = new DatabaseTransactionMiddleware($entityManagerMock);
        $sutMiddleware->__invoke($fakeCommand, $middleWare);
        $this->assertTrue($middleWare->isCalled());
    }

    private function fakeCommand(): CommandInterface
    {
        return new class implements CommandInterface {};
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

    private function middlewareWithExceptionSpy(): callable
    {
        return new class {
            private bool $isCalled = false;

            public function __invoke(CommandInterface $command): void
            {
                $this->isCalled = true;

                throw new \Error();
            }

            public function isCalled(): bool
            {
                return $this->isCalled;
            }
        };
    }
}
