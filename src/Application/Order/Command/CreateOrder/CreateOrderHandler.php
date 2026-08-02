<?php

declare(strict_types=1);

namespace App\Application\Order\Command\CreateOrder;

use App\Application\Order\DTO\OrderResponse;
use App\Domain\Order\Model\Money;
use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Model\OrderLine;
use App\Domain\Order\Model\ProductSku;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Event\EventBus;

final readonly class CreateOrderHandler implements CommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EventBus $eventBus,
    ) {
    }

    public static function handles(): string
    {
        return CreateOrderCommand::class;
    }

    public function __invoke(Command $command): OrderResponse
    {
        assert($command instanceof CreateOrderCommand);

        $lines = [];

        foreach ($command->lines as $line) {
            $lines[] = new OrderLine(
                new ProductSku($line['productSku']),
                $line['quantity'],
                Money::fromEuros($line['unitPrice']),
            );
        }

        $order = Order::place(OrderId::generate(), $lines);
        $this->orderRepository->save($order);
        $this->eventBus->publish(...$order->pullDomainEvents());

        return OrderResponse::fromDomain($order);
    }
}
