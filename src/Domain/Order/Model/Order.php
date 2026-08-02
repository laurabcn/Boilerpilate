<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

use App\Domain\Order\Event\OrderCancelled;
use App\Domain\Order\Event\OrderCreated;
use App\Domain\Order\Exception\InvalidOrderException;
use App\Domain\Order\Exception\OrderAlreadyCancelledException;
use App\Shared\Domain\AggregateRoot;

final class Order extends AggregateRoot
{
    /** @var list<OrderLine> */
    private array $lines;

    /**
     * @param list<OrderLine> $lines
     */
    private function __construct(
        private OrderId $id,
        private OrderStatus $status,
        array $lines,
        private \DateTimeImmutable $createdAt,
    ) {
        if ([] === $lines) {
            throw InvalidOrderException::emptyLines();
        }

        $this->lines = $lines;
    }

    /**
     * @param list<OrderLine> $lines
     */
    public static function place(OrderId $id, array $lines, ?\DateTimeImmutable $createdAt = null): self
    {
        $order = new self($id, OrderStatus::Placed, $lines, $createdAt ?? new \DateTimeImmutable());
        $order->record(new OrderCreated($id->value()));

        return $order;
    }

    public function cancel(): void
    {
        if ($this->status->isCancelled()) {
            throw OrderAlreadyCancelledException::withId($this->id->value());
        }

        $this->status = OrderStatus::Cancelled;
        $this->record(new OrderCancelled($this->id->value()));
    }

    /**
     * @param list<OrderLine> $lines
     */
    public static function reconstitute(
        OrderId $id,
        OrderStatus $status,
        array $lines,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self($id, $status, $lines, $createdAt);
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return list<OrderLine>
     */
    public function lines(): array
    {
        return $this->lines;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function total(): Money
    {
        $total = new Money(0);

        foreach ($this->lines as $line) {
            $total = $total->add($line->lineTotal());
        }

        return $total;
    }
}
