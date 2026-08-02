<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract readonly class UuidValueObject
{
    private const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    final public function __construct(private string $value)
    {
        if (1 !== preg_match(self::PATTERN, $value)) {
            throw new \InvalidArgumentException(sprintf('Invalid UUID: %s', $value));
        }
    }

    public static function generate(): static
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return new static(vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4)
        ));
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
