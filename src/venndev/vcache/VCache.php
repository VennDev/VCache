<?php

declare(strict_types=1);

namespace venndev\vcache;

use Generator;
use pocketmine\plugin\PluginBase;
use venndev\vcache\handler\CacheHandler;
use venndev\vcache\data\Cache;

final class VCache
{
    use CacheHandler;

    public function __construct(
        private readonly PluginBase $plugin, private float $timeToClear = 60
    )
    {
        $this->timeToClear = microtime(true);
        $plugin->getScheduler()->scheduleRepeatingTask(new tasks\ServerTickTask($this), 20);
    }

    public function clean(): Generator
    {
        /**
         * @var Cache $value
         * @var string $key
         */
        foreach ($this->getCache() as $key => $value) {
            if (is_string($value)) $value = Cache::fromString($value);
            if ($value->isExpired() || $value->getTimeElapsed() > $this->timeToClear) $this->delete($key);
            yield;
        }
    }

    /**
     * This method is used to cache data.
     * @param mixed $key The key to cache the data.
     * @param callable $get The function to get the data.
     * @param callable $callback The function to call when the data is cached.
     * @param bool $hashData Whether to hash the data.
     * @param float $timeExpired The time the cache will expire.
     * @return void
     *
     * Example:
     *      $cache->doCache('my_key', function() {
     *          return 'Hello World';
     *      }, function($data) {
     *          echo $data;
     *      });
     **/
    public function doCache(
        mixed $key, callable $get, callable $callback, bool $hashData = false, float $timeExpired = self::DEFAULT_EXPIRATION_TIME
    ): void
    {
        $key = md5(json_encode($key)); // Convert the key to a string
        if ($this->has($key)) {
            call_user_func($callback, $this->getRaw($key, $hashData)?->getData()); // Call the callback function
        } else {
            $value = call_user_func($get); // Call the get function
            $this->add($key, $value, $timeExpired, $hashData);
        }
    }

    /**
     * This method is used to cache data without spamming the cache.
     * @param mixed $key The key to cache the data.
     * @param callable $get The function to get the data.
     * @param callable $callback The function to call when the data is cached.
     * @param bool $hashData Whether to hash the data.
     * @param float $timeExpired The time the cache will expire.
     * @return void
     *
     * Example:
     *      $cache->doNoSpamCache('my_key', function() {
     *          return 'Hello World';
     *      }, function($data) {
     *          echo $data;
     *      });
     **/
    public function doNoSpamCache(
        mixed $key, callable $get, callable $callback, bool $hashData = false, float $timeExpired = self::DEFAULT_EXPIRATION_TIME
    ): void
    {
        $key = md5(json_encode($key)); // Convert the key to a string
        if (!$this->has($key)) {
            $value = call_user_func($get);
            call_user_func($callback, $value); // Call the callback function
            $this->add($key, $value, $timeExpired, $hashData);
        }
    }

    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

}