<?php

declare(strict_types=1);

namespace App\Domain\Order\Exception;

use App\Shared\Domain\DomainException;

final class OrderNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Order "%s" was not found.', $id), 'ORDER_NOT_FOUND');
    }
}
