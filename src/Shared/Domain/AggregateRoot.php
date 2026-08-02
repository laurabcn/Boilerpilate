<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class AggregateRoot
{
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    final protected function record(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return list<DomainEvent>
     */
    final public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
