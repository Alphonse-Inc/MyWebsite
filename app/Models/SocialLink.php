<?php

declare(strict_types=1);

namespace App\Models;

final class SocialLink extends Model
{
    public static function visible(): array
    {
        return self::rows('SELECT * FROM social_links WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    }

    public static function allRows(): array
    {
        return self::rows('SELECT * FROM social_links ORDER BY sort_order ASC, id ASC');
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM social_links WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO social_links (platform, label, url, sort_order, is_visible) VALUES (?, ?, ?, ?, ?)',
            [$data['platform'], $data['label'], $data['url'], $data['sort_order'], $data['is_visible']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE social_links SET platform = ?, label = ?, url = ?, sort_order = ?, is_visible = ? WHERE id = ?',
            [$data['platform'], $data['label'], $data['url'], $data['sort_order'], $data['is_visible'], $id]
        );
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM social_links WHERE id = ?', [$id]);
    }
}
