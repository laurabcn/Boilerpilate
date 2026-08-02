<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Domain\Order\Model\Money;
use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Model\OrderLine;
use App\Domain\Order\Model\OrderStatus;
use App\Domain\Order\Model\ProductSku;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderDoctrineEntity;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderLineDoctrineEntity;

final class OrderMapper
{
    public function toDoctrine(Order $order, ?OrderDoctrineEntity $entity = null): OrderDoctrineEntity
    {
        $entity ??= new OrderDoctrineEntity();
        $entity->setId($order->id()->value());
        $entity->setStatus($order->status()->value);
        $entity->setTotalCents($order->total()->amountInCents());
        $entity->setCurrency($order->total()->currency());
        $entity->setCreatedAt($order->createdAt());
        $entity->clearLines();

        foreach ($order->lines() as $line) {
            $lineEntity = new OrderLineDoctrineEntity();
            $lineEntity->setProductSku($line->productSku()->value());
            $lineEntity->setQuantity($line->quantity());
            $lineEntity->setUnitPriceCents($line->unitPrice()->amountInCents());
            $lineEntity->setCurrency($line->unitPrice()->currency());
            $entity->addLine($lineEntity);
        }

        return $entity;
    }

    public function toDomain(OrderDoctrineEntity $entity): Order
    {
        $lines = [];

        foreach ($entity->getLines() as $lineEntity) {
            $lines[] = new OrderLine(
                new ProductSku($lineEntity->getProductSku()),
                $lineEntity->getQuantity(),
                new Money($lineEntity->getUnitPriceCents(), $lineEntity->getCurrency()),
            );
        }

        return Order::reconstitute(
            OrderId::fromString($entity->getId()),
            OrderStatus::from($entity->getStatus()),
            $lines,
            $entity->getCreatedAt(),
        );
    }
}
