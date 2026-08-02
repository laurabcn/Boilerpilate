<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Event\EventBus;
use App\Shared\Domain\DomainEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyEventBus implements EventBus
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event, $event->eventName());
        }
    }
}
