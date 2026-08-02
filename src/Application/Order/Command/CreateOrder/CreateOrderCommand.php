<?php

declare(strict_types=1);

namespace App\Application\Order\Command\CreateOrder;

use App\Shared\Application\Command\Command;

final readonly class CreateOrderCommand implements Command
{
    /**
     * @param list<array{productSku: string, quantity: int, unitPrice: float}> $lines
     */
    public function __construct(
        public array $lines,
    ) {
    }
}
