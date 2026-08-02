<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Order\Model\Order;
use App\Domain\Order\Model\OrderId;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderDoctrineEntity;
use App\Infrastructure\Persistence\Doctrine\Mapper\OrderMapper;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderMapper $mapper,
    ) {
    }

    public function save(Order $order): void
    {
        $existing = $this->entityManager->find(OrderDoctrineEntity::class, $order->id()->value());
        $entity = $this->mapper->toDoctrine($order, $existing);
        $this->entityManager->persist($entity);
    }

    public function findById(OrderId $id): ?Order
    {
        $entity = $this->entityManager->find(OrderDoctrineEntity::class, $id->value());

        if (null === $entity) {
            return null;
        }

        return $this->mapper->toDomain($entity);
    }

    public function findAll(int $page, int $limit): array
    {
        /** @var list<OrderDoctrineEntity> $entities */
        $entities = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderDoctrineEntity::class, 'o')
            ->orderBy('o.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(
            fn (OrderDoctrineEntity $entity): Order => $this->mapper->toDomain($entity),
            $entities,
        );
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from(OrderDoctrineEntity::class, 'o')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
