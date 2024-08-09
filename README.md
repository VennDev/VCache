# VCache
- Virion helps minimize data query processing or anti-spam data repeating multiple times at a time. It also helps query data quickly! for PocketMine-PMMP

# Example
```php
$this->cache = new VCache($this);
$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
    $this->cache->doCache("opdiff", function (): string {
        return "opdiff";
    }, function (string $data): void {
        $this->getServer()->broadcastMessage($data);
    },
    true,
    10);
}), 20);
```
