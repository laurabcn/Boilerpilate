<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderDoctrineEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(length: 32)]
    private string $status;

    #[ORM\Column(name: 'total_cents', type: 'integer')]
    private int $totalCents;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, OrderLineDoctrineEntity> */
    #[ORM\OneToMany(
        targetEntity: OrderLineDoctrineEntity::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
        fetch: 'EAGER',
    )]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTotalCents(): int
    {
        return $this->totalCents;
    }

    public function setTotalCents(int $totalCents): void
    {
        $this->totalCents = $totalCents;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection<int, OrderLineDoctrineEntity>
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function clearLines(): void
    {
        $this->lines->clear();
    }

    public function addLine(OrderLineDoctrineEntity $line): void
    {
        if (!$this->lines->contains($line)) {
            $this->lines->add($line);
            $line->setOrder($this);
        }
    }
}
