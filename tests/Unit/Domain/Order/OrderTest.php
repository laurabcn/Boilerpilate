<?php

declare(strict_types=1);

use App\Domain\Order\Exception\InvalidOrderException;
use App\Domain\Order\Exception\OrderAlreadyCancelledException;
use App\Domain\Order\Model\Money;
use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Model\OrderLine;
use App\Domain\Order\Model\OrderStatus;
use App\Domain\Order\Model\ProductSku;

it('places an order with calculated total and domain event', function (): void {
    $order = Order::place(
        OrderId::generate(),
        [
            new OrderLine(new ProductSku('SKU-1'), 2, Money::fromEuros(10.0)),
            new OrderLine(new ProductSku('SKU-2'), 1, Money::fromEuros(5.5)),
        ],
    );

    expect($order->status())->toBe(OrderStatus::Placed)
        ->and($order->total()->amountInCents())->toBe(2550)
        ->and($order->pullDomainEvents())->toHaveCount(1);
});

it('rejects empty order lines', function (): void {
    Order::place(OrderId::generate(), []);
})->throws(InvalidOrderException::class);

it('cancels a placed order once', function (): void {
    $order = Order::place(
        OrderId::generate(),
        [new OrderLine(new ProductSku('SKU-1'), 1, Money::fromEuros(9.99))],
    );
    $order->pullDomainEvents();

    $order->cancel();

    expect($order->status())->toBe(OrderStatus::Cancelled)
        ->and($order->pullDomainEvents())->toHaveCount(1);

    $order->cancel();
})->throws(OrderAlreadyCancelledException::class);
