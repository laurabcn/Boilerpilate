<?php

declare(strict_types=1);

namespace App\Domain\Order\Exception;

use App\Shared\Domain\DomainException;

final class OrderAlreadyCancelledException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Order "%s" is already cancelled.', $id), 'ORDER_ALREADY_CANCELLED');
    }
}
