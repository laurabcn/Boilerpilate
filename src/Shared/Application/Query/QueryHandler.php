<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

interface QueryHandler
{
    public static function handles(): string;

    public function __invoke(Query $query): mixed;
}
