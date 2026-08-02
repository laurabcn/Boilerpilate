<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLineDoctrineEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: OrderDoctrineEntity::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private OrderDoctrineEntity $order;

    #[ORM\Column(name: 'product_sku', length: 64)]
    private string $productSku;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(name: 'unit_price_cents', type: 'integer')]
    private int $unitPriceCents;

    #[ORM\Column(length: 3)]
    private string $currency;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): OrderDoctrineEntity
    {
        return $this->order;
    }

    public function setOrder(OrderDoctrineEntity $order): void
    {
        $this->order = $order;
    }

    public function getProductSku(): string
    {
        return $this->productSku;
    }

    public function setProductSku(string $productSku): void
    {
        $this->productSku = $productSku;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnitPriceCents(): int
    {
        return $this->unitPriceCents;
    }

    public function setUnitPriceCents(int $unitPriceCents): void
    {
        $this->unitPriceCents = $unitPriceCents;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
}
