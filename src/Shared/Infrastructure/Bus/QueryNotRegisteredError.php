<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

final class QueryNotRegisteredError extends \RuntimeException
{
    public function __construct(string $queryClass)
    {
        parent::__construct(sprintf('No handler registered for query "%s".', $queryClass));
    }
}
