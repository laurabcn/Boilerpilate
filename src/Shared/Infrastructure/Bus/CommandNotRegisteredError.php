<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

final class CommandNotRegisteredError extends \RuntimeException
{
    public function __construct(string $commandClass)
    {
        parent::__construct(sprintf('No handler registered for command "%s".', $commandClass));
    }
}
