<?php

declare(strict_types=1);

namespace App\Tests\Domain\Events;

use App\Domain\Events\DomainEventInterface;
use App\Domain\Events\DomainEventPublisher;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Domain\Events\DomainEventPublisher
 * @covers \App\Domain\Models\DomainEventModel
 *
 * @internal
 */
class DomainEventPublisherTest extends TestCase
{
    protected function setUp(): void
    {
        DomainEventPublisher::instance()->reset();
        DomainEventPublisher::clear();
    }

    public function testInstanceIsUnique()
    {
        $domainEventPublisher1 = DomainEventPublisher::instance();
        $domainEventPublisher2 = DomainEventPublisher::instance();
        $this->assertEquals($domainEventPublisher2, $domainEventPublisher1);
    }

    public function testInstanceIsNotClonable()
    {
        $domainEventPublisher1 = DomainEventPublisher::instance();
        $this->expectException(\BadMethodCallException::class);
        $domainEventPublisher2 = clone $domainEventPublisher1;
    }

    public function testPushAndRetrieve()
    {
        $domainEventPublisher1 = DomainEventPublisher::instance();
        $domainEvent1 = $this->fakeDomainEvent();
        $domainEvent2 = $this->fakeDomainEvent();
        $domainEvent3 = $this->fakeDomainEvent();

        $domainEventPublisher1->publish($domainEvent1);
        $domainEventPublisher1->publish($domainEvent2);
        $domainEventPublisher1->publish($domainEvent3);

        $retrieved = $domainEventPublisher1->retrieve();

        $this->assertEquals([$domainEvent1, $domainEvent2, $domainEvent3], $retrieved);
        $this->assertEquals('\test\class', $retrieved[0]->getClass());
    }

    private function fakeDomainEvent(): DomainEventInterface
    {
        return new class implements DomainEventInterface {
            public function getClass(): string
            {
                return '\test\class';
            }
        };
    }
}
