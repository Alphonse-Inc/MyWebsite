<?php

declare(strict_types=1);

namespace App\Models;

final class PasswordReset extends Model
{
    public static function create(int $userId, string $tokenHash, string $expires): void
    {
        self::exec('DELETE FROM password_resets WHERE user_id = ?', [$userId]);
        self::insert(
            'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (?, ?, ?, ?)',
            [$userId, $tokenHash, $expires, date('Y-m-d H:i:s')]
        );
    }

    public static function findValid(string $tokenHash): ?array
    {
        return self::one(
            'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > ?',
            [$tokenHash, date('Y-m-d H:i:s')]
        );
    }

    public static function markUsed(int $id): void
    {
        self::exec('UPDATE password_resets SET used_at = ? WHERE id = ?', [date('Y-m-d H:i:s'), $id]);
    }
}
