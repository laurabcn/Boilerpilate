<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache\Redis;

use App\Application\Order\Port\OrderCacheInterface;
use Psr\Cache\CacheItemPoolInterface;

final readonly class RedisOrderCache implements OrderCacheInterface
{
    private const KEY_PREFIX = 'order.';

    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function get(string $orderId): ?array
    {
        $item = $this->cache->getItem(self::KEY_PREFIX.$orderId);

        if (!$item->isHit()) {
            return null;
        }

        /** @var array<string, mixed> $value */
        $value = $item->get();

        return $value;
    }

    public function set(string $orderId, array $payload, int $ttlSeconds = 60): void
    {
        $item = $this->cache->getItem(self::KEY_PREFIX.$orderId);
        $item->set($payload);
        $item->expiresAfter($ttlSeconds);
        $this->cache->save($item);
    }

    public function delete(string $orderId): void
    {
        $this->cache->deleteItem(self::KEY_PREFIX.$orderId);
    }
}
