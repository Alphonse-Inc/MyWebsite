<?php

declare(strict_types=1);

namespace App\Core;

final class Cache
{
    private static function dir(): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/cache';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private static function file(string $key): string
    {
        return self::dir() . '/' . hash('sha256', $key) . '.cache';
    }

    public static function get(string $key): mixed
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return null;
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            return null;
        }
        $data = unserialize($raw, ['allowed_classes' => false]);
        if (!is_array($data) || ($data['exp'] ?? 0) < time()) {
            @unlink($file);
            return null;
        }
        return $data['v'] ?? null;
    }

    public static function put(string $key, mixed $value, int $ttl = 3600): void
    {
        $payload = serialize(['exp' => time() + $ttl, 'v' => $value]);
        file_put_contents(self::file($key), $payload, LOCK_EX);
    }

    public static function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        $cached = self::get($key);
        if ($cached !== null) {
            return $cached;
        }
        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }

    public static function flush(): void
    {
        foreach (glob(self::dir() . '/*.cache') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
