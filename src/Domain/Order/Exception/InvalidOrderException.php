<?php

declare(strict_types=1);

namespace App\Domain\Order\Exception;

use App\Shared\Domain\DomainException;

final class InvalidOrderException extends DomainException
{
    public static function emptyLines(): self
    {
        return new self('An order must contain at least one line.', 'ORDER_EMPTY_LINES');
    }

    public static function invalidQuantity(int $quantity): self
    {
        return new self(sprintf('Invalid quantity: %d.', $quantity), 'ORDER_INVALID_QUANTITY');
    }

    public static function invalidSku(string $sku): self
    {
        return new self(sprintf('Invalid product SKU: "%s".', $sku), 'ORDER_INVALID_SKU');
    }

    public static function negativeMoney(): self
    {
        return new self('Money amount cannot be negative.', 'ORDER_NEGATIVE_MONEY');
    }

    public static function invalidCurrency(string $currency): self
    {
        return new self(sprintf('Invalid currency: "%s".', $currency), 'ORDER_INVALID_CURRENCY');
    }

    public static function currencyMismatch(string $left, string $right): self
    {
        return new self(sprintf('Currency mismatch: %s vs %s.', $left, $right), 'ORDER_CURRENCY_MISMATCH');
    }
}
