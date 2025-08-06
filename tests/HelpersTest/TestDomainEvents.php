<?php

declare(strict_types=1);

namespace App\Tests\HelpersTest;

use App\Domain\Events\DomainEventPublisher;

trait TestDomainEvents
{
    public function domainEventExists(string $classEvent): void
    {
        // test event has been generated.
        $events = DomainEventPublisher::instance()->retrieve();
        $this->assertContainsOnlyInstancesOf($classEvent, $events);
        $event = $events[0];
        $this->assertNotNull($event->getClass());
        DomainEventPublisher::instance()->reset();
    }
}
