<?php

declare(strict_types=1);

use App\Application\Order\Command\CreateOrder\CreateOrderCommand;
use App\Application\Order\Command\CreateOrder\CreateOrderHandler;
use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Shared\Application\Event\EventBus;
use App\Shared\Domain\DomainEvent;

it('creates an order through the application handler', function (): void {
    $state = new class {
        public ?Order $saved = null;
        /** @var list<DomainEvent> */
        public array $events = [];
    };

    $repository = new class($state) implements OrderRepositoryInterface {
        public function __construct(private object $state)
        {
        }

        public function save(Order $order): void
        {
            $this->state->saved = $order;
        }

        public function findById(OrderId $id): ?Order
        {
            return null;
        }

        public function findAll(int $page, int $limit): array
        {
            return [];
        }

        public function count(): int
        {
            return 0;
        }
    };

    $eventBus = new class($state) implements EventBus {
        public function __construct(private object $state)
        {
        }

        public function publish(DomainEvent ...$events): void
        {
            foreach ($events as $event) {
                $this->state->events[] = $event;
            }
        }
    };

    $handler = new CreateOrderHandler($repository, $eventBus);
    $response = $handler(new CreateOrderCommand([
        ['productSku' => 'SKU-1', 'quantity' => 2, 'unitPrice' => 12.5],
    ]));

    expect($response->status)->toBe('placed')
        ->and($response->total)->toBe(25.0)
        ->and($state->saved)->toBeInstanceOf(Order::class)
        ->and($state->events)->toHaveCount(1);
});
