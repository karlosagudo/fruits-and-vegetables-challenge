<?php

declare(strict_types=1);

namespace App\Domain\Events;

final class DomainEventPublisher
{
    private static ?DomainEventPublisher $domainEventPublisher = null;

    /** @var DomainEventInterface[] */
    private array $domainEvents = [];

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public static function instance(): self
    {
        if (!self::$domainEventPublisher instanceof DomainEventPublisher) {
            self::$domainEventPublisher = new self();
        }

        return self::$domainEventPublisher;
    }

    public function publish(DomainEventInterface $aDomainEvent): void
    {
        $this->domainEvents[] = $aDomainEvent;
    }

    /**
     * @return DomainEventInterface[] array
     */
    public function retrieve(): array
    {
        return $this->domainEvents;
    }

    public function reset(): void
    {
        $this->domainEvents = [];
    }

    public static function clear(): void
    {
        self::$domainEventPublisher = null;
    }
}
