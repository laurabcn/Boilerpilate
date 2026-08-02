<?php

declare(strict_types=1);

namespace App\Application\Order\Query\ListOrders;

use App\Shared\Application\Query\Query;

final readonly class ListOrdersQuery implements Query
{
    public function __construct(
        public int $page = 1,
        public int $limit = 20,
    ) {
    }
}
