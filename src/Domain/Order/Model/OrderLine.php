<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

use App\Domain\Order\Exception\InvalidOrderException;

final readonly class OrderLine
{
    public function __construct(
        private ProductSku $productSku,
        private int $quantity,
        private Money $unitPrice,
    ) {
        if ($quantity < 1) {
            throw InvalidOrderException::invalidQuantity($quantity);
        }
    }

    public function productSku(): ProductSku
    {
        return $this->productSku;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function lineTotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
