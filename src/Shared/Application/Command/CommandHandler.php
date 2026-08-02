<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

interface CommandHandler
{
    public static function handles(): string;

    public function __invoke(Command $command): mixed;
}
