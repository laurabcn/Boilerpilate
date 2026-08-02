<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Command\CommandHandler;

final class InMemoryCommandBus implements CommandBus
{
    /** @var array<class-string, CommandHandler> */
    private array $handlers = [];

    /**
     * @param iterable<CommandHandler> $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            /** @var class-string $commandClass */
            $commandClass = $handler::handles();
            $this->handlers[$commandClass] = $handler;
        }
    }

    public function dispatch(Command $command): mixed
    {
        $commandClass = $command::class;

        if (!isset($this->handlers[$commandClass])) {
            throw new CommandNotRegisteredError($commandClass);
        }

        return ($this->handlers[$commandClass])($command);
    }
}
