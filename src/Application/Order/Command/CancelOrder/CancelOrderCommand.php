<?php

declare(strict_types=1);

namespace App\Application\Order\Command\CancelOrder;

use App\Shared\Application\Command\Command;

final readonly class CancelOrderCommand implements Command
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
