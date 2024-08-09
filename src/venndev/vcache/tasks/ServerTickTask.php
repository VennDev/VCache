<?php

declare(strict_types=1);

namespace venndev\vcache\tasks;

use Generator;
use Throwable;
use pocketmine\scheduler\Task;
use venndev\vcache\VCache;
use vennv\vapm\CoroutineGen;

final class ServerTickTask extends Task
{
    private bool $isRunning = false;

    public function __construct(private readonly VCache $cache)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @throws Throwable
     */
    public function onRun(): void
    {
        if (!$this->isRunning) {
            $this->isRunning = true;
            CoroutineGen::runBlocking(function (): Generator {
                yield from $this->cache->clean();
                return $this->isRunning = false;
            });
        }
    }

    public function getCache(): VCache
    {
        return $this->cache;
    }

}