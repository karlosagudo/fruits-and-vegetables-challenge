<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Bus;

use App\Application\CommandInterface;
use App\Infrastructure\Bus\CompleteCommandBus;
use App\Infrastructure\Bus\MiddlewarePipelineFactory;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Infrastructure\Bus\CompleteCommandBus
 *
 * @internal
 */
class CompleteCommandBusTest extends TestCase
{
    public function testCreatePipelineAndPassCommand()
    {
        $pipeline = $this->createMock(MiddlewarePipelineFactory::class);
        $pipeline
            ->expects($this->once())
            ->method('create')
            ->willReturn(function (CommandInterface $command) {})
        ;
        $sut = new CompleteCommandBus($pipeline);
        $sut->handle($this->commandDummy());
    }

    private function commandDummy(): CommandInterface
    {
        return new class implements CommandInterface {};
    }
}
