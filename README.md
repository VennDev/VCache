# VCache
- Virion helps minimize data query processing or anti-spam data repeating multiple times at a time. It also helps query data quickly! for PocketMine-PMMP

# Example
```php
$this->cache = new VCache($this);
$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {

    // This is fast query processing!
    $this->cache->doCache("This is Key A", function (): string {
        sleep(3); // Let's say something processed takes 3s to complete!
        return "result_data"; // This is the processing of the query or something that you want to return when the processing is done here
    }, function (string $data): void {
        $this->getServer()->broadcastMessage($data);
    },
    true,
    10);

    // This is anti-duplicate handling in addition to fast querying!
    $this->cache->doNoSpamCache(["This is Key B", "ASDASDAS", "ASDASD], function (): string {
        sleep(3); // Let's say something processed takes 3s to complete!
        return "result_data"; // This is the processing of the query or something that you want to return when the processing is done here
    }, function (string $data): void {
        $this->getServer()->broadcastMessage($data);
    },
    true,
    10);
}), 20);
```
