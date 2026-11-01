<?php

declare(strict_types=1);

namespace App\Models;

final class Service extends Model
{
    public static function visible(): array
    {
        return self::rows('SELECT * FROM services WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    }

    public static function allRows(): array
    {
        return self::rows('SELECT * FROM services ORDER BY sort_order ASC, id ASC');
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM services WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO services (name, description, icon, sort_order, is_visible) VALUES (?, ?, ?, ?, ?)',
            [$data['name'], $data['description'], $data['icon'], $data['sort_order'], $data['is_visible']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE services SET name = ?, description = ?, icon = ?, sort_order = ?, is_visible = ? WHERE id = ?',
            [$data['name'], $data['description'], $data['icon'], $data['sort_order'], $data['is_visible'], $id]
        );
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM services WHERE id = ?', [$id]);
    }

    /** @param list<int> $ids */
    public static function reorder(array $ids): void
    {
        $order = 1;
        foreach ($ids as $id) {
            self::exec('UPDATE services SET sort_order = ? WHERE id = ?', [$order, $id]);
            $order++;
        }
    }
}
