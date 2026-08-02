<?php

declare(strict_types=1);

namespace App\Application\Order\Query\GetOrderById;

use App\Application\Order\DTO\OrderResponse;
use App\Application\Order\Port\OrderCacheInterface;
use App\Domain\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;

final readonly class GetOrderByIdHandler implements QueryHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderCacheInterface $orderCache,
    ) {
    }

    public static function handles(): string
    {
        return GetOrderByIdQuery::class;
    }

    public function __invoke(Query $query): OrderResponse
    {
        assert($query instanceof GetOrderByIdQuery);

        $cached = $this->orderCache->get($query->orderId);

        if (null !== $cached) {
            return $this->hydrateFromCache($cached);
        }

        $order = $this->orderRepository->findById(OrderId::fromString($query->orderId));

        if (null === $order) {
            throw OrderNotFoundException::withId($query->orderId);
        }

        $response = OrderResponse::fromDomain($order);
        $this->orderCache->set($query->orderId, $response->toArray());

        return $response;
    }

    /**
     * @param array<string, mixed> $cached
     */
    private function hydrateFromCache(array $cached): OrderResponse
    {
        $lines = [];

        foreach ($cached['lines'] as $line) {
            $lines[] = new \App\Application\Order\DTO\OrderLineResponse(
                $line['productSku'],
                $line['quantity'],
                $line['unitPrice'],
                $line['lineTotal'],
                $line['currency'],
            );
        }

        return new OrderResponse(
            $cached['id'],
            $cached['status'],
            $cached['total'],
            $cached['currency'],
            $cached['createdAt'],
            $lines,
        );
    }
}
