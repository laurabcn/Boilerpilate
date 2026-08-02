<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

use App\Domain\Order\Exception\InvalidOrderException;

final readonly class Money
{
    public function __construct(
        private int $amountInCents,
        private string $currency = 'EUR',
    ) {
        if ($amountInCents < 0) {
            throw InvalidOrderException::negativeMoney();
        }

        if (3 !== strlen($currency)) {
            throw InvalidOrderException::invalidCurrency($currency);
        }
    }

    public static function fromEuros(float $euros, string $currency = 'EUR'): self
    {
        return new self((int) round($euros * 100), strtoupper($currency));
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw InvalidOrderException::currencyMismatch($this->currency, $other->currency);
        }

        return new self($this->amountInCents + $other->amountInCents, $this->currency);
    }

    public function multiply(int $quantity): self
    {
        return new self($this->amountInCents * $quantity, $this->currency);
    }

    public function toFloat(): float
    {
        return $this->amountInCents / 100;
    }
}
