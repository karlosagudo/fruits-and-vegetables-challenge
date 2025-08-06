<?php

declare(strict_types=1);

namespace App\Tests\Domain\Models;

use App\Domain\Events\DomainEventInterface;
use App\Domain\Models\DomainEventModel;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @internal
 *
 * @covers \App\Domain\Models\DomainEventModel
 */
class DomainModelTest extends TestCase
{
    public function testDomainModel(): void
    {
        $domainModel = new DomainEventModel(
            'id',
            1,
            '\test\class',
            $this->fakeDomainEvent()
        );
        $this->assertEquals('\test\class', $domainModel->getClass());
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
}
