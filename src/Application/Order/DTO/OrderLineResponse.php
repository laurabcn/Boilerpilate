<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

final readonly class OrderLineResponse
{
    public function __construct(
        public string $productSku,
        public int $quantity,
        public float $unitPrice,
        public float $lineTotal,
        public string $currency,
    ) {
    }

    /**
     * @return array{productSku: string, quantity: int, unitPrice: float, lineTotal: float, currency: string}
     */
    public function toArray(): array
    {
        return [
            'productSku' => $this->productSku,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'lineTotal' => $this->lineTotal,
            'currency' => $this->currency,
        ];
    }
}
