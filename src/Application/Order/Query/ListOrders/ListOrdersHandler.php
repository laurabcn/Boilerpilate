<?php

declare(strict_types=1);

namespace App\Application\Order\Query\ListOrders;

use App\Application\Order\DTO\OrderListResponse;
use App\Application\Order\DTO\OrderResponse;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;

final readonly class ListOrdersHandler implements QueryHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public static function handles(): string
    {
        return ListOrdersQuery::class;
    }

    public function __invoke(Query $query): OrderListResponse
    {
        assert($query instanceof ListOrdersQuery);

        $page = max(1, $query->page);
        $limit = min(100, max(1, $query->limit));

        $orders = $this->orderRepository->findAll($page, $limit);
        $items = array_map(
            static fn ($order): OrderResponse => OrderResponse::fromDomain($order),
            $orders,
        );

        return new OrderListResponse(
            $items,
            $page,
            $limit,
            $this->orderRepository->count(),
        );
    }
}
