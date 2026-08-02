<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use App\Domain\Order\Model\Order;

final readonly class OrderResponse
{
    /**
     * @param list<OrderLineResponse> $lines
     */
    public function __construct(
        public string $id,
        public string $status,
        public float $total,
        public string $currency,
        public string $createdAt,
        public array $lines,
    ) {
    }

    public static function fromDomain(Order $order): self
    {
        $lines = [];

        foreach ($order->lines() as $line) {
            $lines[] = new OrderLineResponse(
                $line->productSku()->value(),
                $line->quantity(),
                $line->unitPrice()->toFloat(),
                $line->lineTotal()->toFloat(),
                $line->unitPrice()->currency(),
            );
        }

        return new self(
            $order->id()->value(),
            $order->status()->value,
            $order->total()->toFloat(),
            $order->total()->currency(),
            $order->createdAt()->format(\DateTimeInterface::ATOM),
            $lines,
        );
    }

    /**
     * @return array{
     *     id: string,
     *     status: string,
     *     total: float,
     *     currency: string,
     *     createdAt: string,
     *     lines: list<array{productSku: string, quantity: int, unitPrice: float, lineTotal: float, currency: string}>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total,
            'currency' => $this->currency,
            'createdAt' => $this->createdAt,
            'lines' => array_map(static fn (OrderLineResponse $line): array => $line->toArray(), $this->lines),
        ];
    }
}
