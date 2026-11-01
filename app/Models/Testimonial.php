<?php

declare(strict_types=1);

namespace App\Models;

final class Testimonial extends Model
{
    public static function visible(): array
    {
        return self::rows('SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    }

    public static function allRows(): array
    {
        return self::rows('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC');
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM testimonials WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO testimonials (author_name, author_role, quote, photo_path, sort_order, is_visible) VALUES (?, ?, ?, ?, ?, ?)',
            [$data['author_name'], $data['author_role'], $data['quote'], $data['photo_path'], $data['sort_order'], $data['is_visible']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE testimonials SET author_name = ?, author_role = ?, quote = ?, photo_path = ?, sort_order = ?, is_visible = ? WHERE id = ?',
            [$data['author_name'], $data['author_role'], $data['quote'], $data['photo_path'], $data['sort_order'], $data['is_visible'], $id]
        );
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM testimonials WHERE id = ?', [$id]);
    }
}
