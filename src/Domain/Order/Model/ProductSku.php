<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

use App\Domain\Order\Exception\InvalidOrderException;

final readonly class ProductSku
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ('' === $normalized || strlen($normalized) > 64) {
            throw InvalidOrderException::invalidSku($value);
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
