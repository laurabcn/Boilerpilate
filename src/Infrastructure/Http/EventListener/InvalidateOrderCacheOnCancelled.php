<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\EventListener;

use App\Application\Order\Port\OrderCacheInterface;
use App\Domain\Order\Event\OrderCancelled;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'order.cancelled')]
final readonly class InvalidateOrderCacheOnCancelled
{
    public function __construct(
        private OrderCacheInterface $orderCache,
    ) {
    }

    public function __invoke(OrderCancelled $event): void
    {
        $this->orderCache->delete($event->orderId());
    }
}
