<?php

declare(strict_types=1);

namespace venndev\vcache\handler;

use Generator;
use venndev\vcache\data\Cache;

trait CacheHandler
{
    public const DEFAULT_EXPIRATION_TIME = 3;

    /**
     * @var array<string, Cache|string>
     */
    private array $cache = [];
    
    public function getCache(): Generator
    {
        foreach ($this->cache as $key => $value) yield $key => $value;
    }

    public function add(string $key, mixed $value, float $timeExpired, bool $hashData = false): void
    {
        $cache = new Cache($value, $timeExpired);
        $this->cache[$key] = ($hashData ? $cache->__toString() : $cache);
    }

    public function set(string $key, mixed $value, float $timeExpired): void
    {
        $this->cache[$key] = new Cache($value, $timeExpired);
    }

    public function has(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    public function getRaw(string $key, bool $hashData = false): ?Cache
    {
        $data = $this->cache[$key] ?? null;
        if ($data === null) return null;
        return $hashData ? Cache::fromString($data) : $data;
    }

    public function delete(string $key): void
    {
        if ($this->has($key)) unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->cache = [];
    }

}