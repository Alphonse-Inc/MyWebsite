<?php

declare(strict_types=1);

namespace App\Models;

final class Education extends Model
{
    public static function visible(): array
    {
        return self::rows('SELECT * FROM education WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    }

    public static function allRows(): array
    {
        return self::rows('SELECT * FROM education ORDER BY sort_order ASC, id ASC');
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM education WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO education (title, institution, location, start_label, end_label, description, sort_order, is_visible) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$data['title'], $data['institution'], $data['location'], $data['start_label'], $data['end_label'], $data['description'], $data['sort_order'], $data['is_visible']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE education SET title = ?, institution = ?, location = ?, start_label = ?, end_label = ?, description = ?, sort_order = ?, is_visible = ? WHERE id = ?',
            [$data['title'], $data['institution'], $data['location'], $data['start_label'], $data['end_label'], $data['description'], $data['sort_order'], $data['is_visible'], $id]
        );
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM education WHERE id = ?', [$id]);
    }
}
