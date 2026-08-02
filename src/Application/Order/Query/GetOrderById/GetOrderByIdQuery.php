<?php

declare(strict_types=1);

namespace App\Application\Order\Query\GetOrderById;

use App\Shared\Application\Query\Query;

final readonly class GetOrderByIdQuery implements Query
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
