<?php

declare(strict_types=1);

namespace App\Models;

final class User extends Model
{
    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return self::one('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
    }

    public static function count(): int
    {
        $row = self::one('SELECT COUNT(*) AS total FROM users');
        return (int) ($row['total'] ?? 0);
    }

    public static function touchLogin(int $id): void
    {
        self::exec('UPDATE users SET last_login_at = ?, updated_at = ? WHERE id = ?', [date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        self::exec(
            'UPDATE users SET password_hash = ?, must_change_password = 0, updated_at = ? WHERE id = ?',
            [$hash, date('Y-m-d H:i:s'), $id]
        );
    }

    public static function updateTotp(int $id, ?string $secret, bool $enabled): void
    {
        self::exec(
            'UPDATE users SET totp_secret = ?, totp_enabled = ?, updated_at = ? WHERE id = ?',
            [$secret, $enabled ? 1 : 0, date('Y-m-d H:i:s'), $id]
        );
    }
}
