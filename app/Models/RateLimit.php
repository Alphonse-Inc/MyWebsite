<?php

declare(strict_types=1);

namespace App\Models;

final class RateLimit extends Model
{
    public static function hit(string $bucket, string $ip, int $limit, int $windowSeconds): bool
    {
        $now = date('Y-m-d H:i:s');
        $row = self::one('SELECT * FROM rate_limits WHERE bucket = ? AND ip = ?', [$bucket, $ip]);
        if ($row === null) {
            self::insert(
                'INSERT INTO rate_limits (bucket, ip, hits, window_start) VALUES (?, ?, 1, ?)',
                [$bucket, $ip, $now]
            );
            return true;
        }
        $started = strtotime((string) $row['window_start']) ?: time();
        if ((time() - $started) > $windowSeconds) {
            self::exec('UPDATE rate_limits SET hits = 1, window_start = ? WHERE id = ?', [$now, $row['id']]);
            return true;
        }
        if ((int) $row['hits'] >= $limit) {
            return false;
        }
        self::exec('UPDATE rate_limits SET hits = hits + 1 WHERE id = ?', [$row['id']]);
        return true;
    }
}
