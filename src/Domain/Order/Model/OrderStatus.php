<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

enum OrderStatus: string
{
    case Placed = 'placed';
    case Cancelled = 'cancelled';

    public function isCancelled(): bool
    {
        return self::Cancelled === $this;
    }
}
