<?php

declare(strict_types=1);

namespace App\Models;

final class Category extends Model
{
    public static function allRows(): array
    {
        return self::rows('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public static function findByName(string $name): ?array
    {
        return self::one('SELECT * FROM categories WHERE name = ?', [$name]);
    }

    public static function create(string $name): int
    {
        $slug = slugify($name);
        $base = $slug;
        $i = 2;
        while (self::one('SELECT id FROM categories WHERE slug = ?', [$slug]) !== null) {
            $slug = $base . '-' . $i;
            $i++;
        }
        $row = self::one('SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM categories');
        $sort = (int) ($row['max_sort'] ?? 0) + 1;
        return self::insert(
            'INSERT INTO categories (name, slug, sort_order) VALUES (?, ?, ?)',
            [single_line($name, 80), $slug, $sort]
        );
    }
}
