<?php

declare(strict_types=1);

namespace App\Models;

final class LoginAttempt extends Model
{
    public static function tooMany(string $email, string $ip): bool
    {
        $max = (int) (config('app')['login_max_attempts'] ?? 5);
        $window = (int) (config('app')['login_window'] ?? 900);
        $since = date('Y-m-d H:i:s', time() - $window);
        $row = self::one(
            'SELECT COUNT(*) AS total FROM login_attempts WHERE success = 0 AND attempted_at > ? AND (email = ? OR ip = ?)',
            [$since, $email, $ip]
        );
        return (int) ($row['total'] ?? 0) >= $max;
    }

    public static function record(string $email, string $ip, bool $success): void
    {
        self::insert(
            'INSERT INTO login_attempts (email, ip, success, attempted_at) VALUES (?, ?, ?, ?)',
            [$email, $ip, $success ? 1 : 0, date('Y-m-d H:i:s')]
        );
        if ($success) {
            self::exec('DELETE FROM login_attempts WHERE email = ? AND success = 0', [$email]);
        }
    }
}
