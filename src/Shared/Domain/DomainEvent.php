<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface DomainEvent
{
    public function eventName(): string;

    public function occurredOn(): \DateTimeImmutable;
}
