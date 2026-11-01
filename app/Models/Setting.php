<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    /** @return array<string, string> */
    public static function all(): array
    {
        $rows = self::rows('SELECT setting_key, setting_value FROM settings');
        $out = [];
        foreach ($rows as $row) {
            $out[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
        }
        return $out;
    }

    public static function get(string $key, string $default = ''): string
    {
        $row = self::one('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
        if ($row === null || $row['setting_value'] === null) {
            return $default;
        }
        return (string) $row['setting_value'];
    }

    public static function put(string $key, string $value): void
    {
        $now = date('Y-m-d H:i:s');
        self::exec(
            'INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)',
            [$key, $value, $now]
        );
    }

    /** @param array<string, string> $pairs */
    public static function putMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::put($key, $value);
        }
    }

    public static function increment(string $key): int
    {
        $current = (int) self::get($key, '0');
        $next = $current + 1;
        self::put($key, (string) $next);
        return $next;
    }
}
