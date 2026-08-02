<?php

declare(strict_types=1);

namespace App\Application\Order\Port;

interface OrderCacheInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function get(string $orderId): ?array;

    /**
     * @param array<string, mixed> $payload
     */
    public function set(string $orderId, array $payload, int $ttlSeconds = 60): void;

    public function delete(string $orderId): void;
}
