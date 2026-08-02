<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandBus;
use Doctrine\ORM\EntityManagerInterface;

final readonly class TransactionalCommandBus implements CommandBus
{
    public function __construct(
        private CommandBus $inner,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function dispatch(Command $command): mixed
    {
        $this->entityManager->beginTransaction();

        try {
            $result = $this->inner->dispatch($command);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $result;
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
