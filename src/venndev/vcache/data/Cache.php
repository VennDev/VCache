<?php

declare(strict_types=1);

namespace venndev\vcache\data;

final class Cache
{

    private float $lastTime;

    public function __construct(
        private mixed          $data,
        private readonly float $expirationTime
    )
    {
        $this->lastTime = microtime(true);
    }

    public static function create(mixed $data, float $expirationTime): Cache
    {
        return new Cache($data, $expirationTime);
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * This method returns the last time the cache was accessed.
     * @return float
     **/
    public function getLastTime(): float
    {
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime): Cache
    {
        $this->lastTime = $lastTime;
        return $this;
    }

    public function getTimeElapsed(): float
    {
        return (microtime(true) - $this->lastTime);
    }

    public function isExpired(): bool
    {
        return $this->lastTime < microtime(true) - $this->expirationTime;
    }

    public function updateLastTime(): void
    {
        $this->lastTime = microtime(true);
    }

    public function updateData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * This method returns the string cache class
     * @return string
     */
    public function __toString(): string
    {
        return gzcompress(json_encode([
            'data' => $this->data,
            'expirationTime' => $this->expirationTime,
            'lastTime' => $this->lastTime
        ]));
    }

    /**
     * This method returns the cache class from string
     * @param string $cache
     * @return Cache
     */
    public static function fromString(string $cache): Cache
    {
        $data = json_decode(gzuncompress($cache), true);
        return Cache::create($data['data'], $data['expirationTime'])->setLastTime($data['lastTime']);
    }

}