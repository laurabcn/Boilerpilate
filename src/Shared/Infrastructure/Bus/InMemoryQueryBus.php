<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Application\Query\QueryHandler;

final class InMemoryQueryBus implements QueryBus
{
    /** @var array<class-string, QueryHandler> */
    private array $handlers = [];

    /**
     * @param iterable<QueryHandler> $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            /** @var class-string $queryClass */
            $queryClass = $handler::handles();
            $this->handlers[$queryClass] = $handler;
        }
    }

    public function ask(Query $query): mixed
    {
        $queryClass = $query::class;

        if (!isset($this->handlers[$queryClass])) {
            throw new QueryNotRegisteredError($queryClass);
        }

        return ($this->handlers[$queryClass])($query);
    }
}
