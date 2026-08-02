<?php

declare(strict_types=1);

namespace App\Domain\Order\Repository;

use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(OrderId $id): ?Order;

    /**
     * @return list<Order>
     */
    public function findAll(int $page, int $limit): array;

    public function count(): int;
}
