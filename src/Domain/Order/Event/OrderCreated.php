<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Shared\Domain\DomainEvent;

final readonly class OrderCreated implements DomainEvent
{
    public function __construct(
        private string $orderId,
        private \DateTimeImmutable $occurredOn = new \DateTimeImmutable(),
    ) {
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function eventName(): string
    {
        return 'order.created';
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
