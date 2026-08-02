<?php

declare(strict_types=1);

namespace App\Application\Order\Command\CancelOrder;

use App\Application\Order\DTO\OrderResponse;
use App\Domain\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Event\EventBus;

final readonly class CancelOrderHandler implements CommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EventBus $eventBus,
    ) {
    }

    public static function handles(): string
    {
        return CancelOrderCommand::class;
    }

    public function __invoke(Command $command): OrderResponse
    {
        assert($command instanceof CancelOrderCommand);

        $orderId = OrderId::fromString($command->orderId);
        $order = $this->orderRepository->findById($orderId);

        if (null === $order) {
            throw OrderNotFoundException::withId($command->orderId);
        }

        $order->cancel();
        $this->orderRepository->save($order);
        $this->eventBus->publish(...$order->pullDomainEvents());

        return OrderResponse::fromDomain($order);
    }
}
