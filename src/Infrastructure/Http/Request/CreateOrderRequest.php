<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrderRequest
{
    /**
     * @param list<array{productSku?: string, quantity?: int, unitPrice?: float}> $lines
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Collection([
                'productSku' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Length(max: 64),
                ],
                'quantity' => [
                    new Assert\NotNull(),
                    new Assert\Type('integer'),
                    new Assert\Positive(),
                ],
                'unitPrice' => [
                    new Assert\NotNull(),
                    new Assert\Type('numeric'),
                    new Assert\PositiveOrZero(),
                ],
            ]),
        ])]
        public array $lines = [],
    ) {
    }
}
