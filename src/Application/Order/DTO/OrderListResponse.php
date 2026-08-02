<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

final readonly class OrderListResponse
{
    /**
     * @param list<OrderResponse> $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $limit,
        public int $total,
    ) {
    }

    /**
     * @return array{items: list<array<string, mixed>>, page: int, limit: int, total: int}
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(static fn (OrderResponse $order): array => $order->toArray(), $this->items),
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
        ];
    }
}
