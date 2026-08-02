<?php

declare(strict_types=1);

namespace App\Domain\Order\Model;

use App\Shared\Domain\ValueObject\UuidValueObject;

final readonly class OrderId extends UuidValueObject
{
}
