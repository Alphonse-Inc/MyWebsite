<?php

declare(strict_types=1);

namespace App\Models;

final class Project extends Model
{
    public static function find(int $id): ?array
    {
        $row = self::one(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM projects p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?',
            [$id]
        );
        return $row === null ? null : self::hydrate($row);
    }

    public static function findBySlug(string $slug, bool $publishedOnly = true): ?array
    {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM projects p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ?';
        $params = [$slug];
        if ($publishedOnly) {
            $sql .= ' AND p.status = ?';
            $params[] = 'published';
        }
        $row = self::one($sql, $params);
        return $row === null ? null : self::hydrate($row);
    }

    /** @param array<string, string> $filters */
    public static function adminList(array $filters = []): array
    {
        $sql = 'SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id = p.category_id WHERE 1 = 1';
        $params = [];
        if (($filters['q'] ?? '') !== '') {
            $sql .= ' AND (p.name LIKE ? OR p.short_description LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (($filters['status'] ?? '') !== '') {
            $sql .= ' AND p.status = ?';
            $params[] = $filters['status'];
        }
        if (($filters['category'] ?? '') !== '') {
            $sql .= ' AND p.category_id = ?';
            $params[] = (int) $filters['category'];
        }
        $sql .= ' ORDER BY p.sort_order ASC, p.id ASC';
        $rows = self::rows($sql, $params);
        foreach ($rows as &$row) {
            $row = self::hydrate($row);
            $row['cover'] = self::cover((int) $row['id']);
        }
        unset($row);
        return $rows;
    }

    public static function published(): array
    {
        $rows = self::rows(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM projects p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = ? ORDER BY p.sort_order ASC, p.id ASC',
            ['published']
        );
        foreach ($rows as &$row) {
            $row = self::hydrate($row);
            $row['cover'] = self::cover((int) $row['id']);
        }
        unset($row);
        return $rows;
    }

    public static function countPublished(): int
    {
        $row = self::one('SELECT COUNT(*) AS total FROM projects WHERE status = ?', ['published']);
        return (int) ($row['total'] ?? 0);
    }

    /** @return array{prev:?array, next:?array} */
    public static function neighbors(int $id): array
    {
        $rows = self::rows(
            'SELECT id, name, slug FROM projects WHERE status = ? ORDER BY sort_order ASC, id ASC',
            ['published']
        );
        $prev = null;
        $next = null;
        $count = count($rows);
        for ($i = 0; $i < $count; $i++) {
            if ((int) $rows[$i]['id'] !== $id) {
                continue;
            }
            $prev = $i > 0 ? $rows[$i - 1] : null;
            $next = $i < $count - 1 ? $rows[$i + 1] : null;
            break;
        }
        return ['prev' => $prev, 'next' => $next];
    }

    public static function images(int $projectId): array
    {
        $rows = self::rows(
            'SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC',
            [$projectId]
        );
        foreach ($rows as &$row) {
            $row['meta'] = self::decodeMeta($row['meta'] ?? null);
        }
        unset($row);
        return $rows;
    }

    public static function cover(int $projectId): ?array
    {
        $row = self::one(
            'SELECT * FROM project_images WHERE project_id = ? ORDER BY is_cover DESC, sort_order ASC, id ASC LIMIT 1',
            [$projectId]
        );
        if ($row === null) {
            return null;
        }
        $row['meta'] = self::decodeMeta($row['meta'] ?? null);
        return $row;
    }

    public static function image(int $id, int $projectId): ?array
    {
        $row = self::one('SELECT * FROM project_images WHERE id = ? AND project_id = ?', [$id, $projectId]);
        if ($row === null) {
            return null;
        }
        $row['meta'] = self::decodeMeta($row['meta'] ?? null);
        return $row;
    }

    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        if ($ignoreId === null) {
            return self::one('SELECT id FROM projects WHERE slug = ?', [$slug]) !== null;
        }
        return self::one('SELECT id FROM projects WHERE slug = ? AND id <> ?', [$slug, $ignoreId]) !== null;
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $sort = self::one('SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM projects');
        return self::insert(
            'INSERT INTO projects (category_id, name, slug, short_description, long_description, url, technologies, year, status, featured, sort_order, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['short_description'],
                $data['long_description'],
                $data['url'],
                json_encode($data['technologies'], JSON_UNESCAPED_UNICODE),
                $data['year'],
                $data['status'],
                $data['featured'],
                (int) ($sort['max_sort'] ?? 0) + 1,
                $now,
                $now,
            ]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE projects SET category_id = ?, name = ?, slug = ?, short_description = ?, long_description = ?, url = ?, technologies = ?, year = ?, status = ?, featured = ?, updated_at = ? WHERE id = ?',
            [
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['short_description'],
                $data['long_description'],
                $data['url'],
                json_encode($data['technologies'], JSON_UNESCAPED_UNICODE),
                $data['year'],
                $data['status'],
                $data['featured'],
                date('Y-m-d H:i:s'),
                $id,
            ]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        self::exec('UPDATE projects SET status = ?, updated_at = ? WHERE id = ?', [$status, date('Y-m-d H:i:s'), $id]);
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM projects WHERE id = ?', [$id]);
    }

    /** @param list<int> $ids */
    public static function reorder(array $ids): void
    {
        $order = 1;
        foreach ($ids as $id) {
            self::exec('UPDATE projects SET sort_order = ? WHERE id = ?', [$order, $id]);
            $order++;
        }
    }

    /** @param array<string, mixed> $meta */
    public static function addImage(int $projectId, string $stem, string $alt, int $width, int $height, array $meta, bool $cover): int
    {
        if ($cover) {
            self::exec('UPDATE project_images SET is_cover = 0 WHERE project_id = ?', [$projectId]);
        }
        $sort = self::one('SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM project_images WHERE project_id = ?', [$projectId]);
        return self::insert(
            'INSERT INTO project_images (project_id, stem, alt_text, is_cover, sort_order, width, height, meta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$projectId, $stem, $alt, $cover ? 1 : 0, (int) ($sort['max_sort'] ?? 0) + 1, $width, $height, json_encode($meta)]
        );
    }

    public static function setCover(int $projectId, int $imageId): void
    {
        self::exec('UPDATE project_images SET is_cover = 0 WHERE project_id = ?', [$projectId]);
        self::exec('UPDATE project_images SET is_cover = 1 WHERE project_id = ? AND id = ?', [$projectId, $imageId]);
    }

    public static function updateAlt(int $projectId, int $imageId, string $alt): void
    {
        self::exec('UPDATE project_images SET alt_text = ? WHERE project_id = ? AND id = ?', [$alt, $projectId, $imageId]);
    }

    public static function deleteImage(int $projectId, int $imageId): void
    {
        self::exec('DELETE FROM project_images WHERE project_id = ? AND id = ?', [$projectId, $imageId]);
        $cover = self::one('SELECT id FROM project_images WHERE project_id = ? AND is_cover = 1', [$projectId]);
        if ($cover === null) {
            $first = self::one('SELECT id FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1', [$projectId]);
            if ($first !== null) {
                self::setCover($projectId, (int) $first['id']);
            }
        }
    }

    /** @param list<int> $ids */
    public static function reorderImages(int $projectId, array $ids): void
    {
        $order = 1;
        foreach ($ids as $id) {
            self::exec('UPDATE project_images SET sort_order = ? WHERE project_id = ? AND id = ?', [$order, $projectId, $id]);
            $order++;
        }
    }

    public static function imageCount(int $projectId): int
    {
        $row = self::one('SELECT COUNT(*) AS total FROM project_images WHERE project_id = ?', [$projectId]);
        return (int) ($row['total'] ?? 0);
    }

    /** @param array<string, mixed> $row */
    private static function hydrate(array $row): array
    {
        $tech = $row['technologies'] ?? '[]';
        $decoded = is_string($tech) ? json_decode($tech, true) : [];
        $row['technologies'] = is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
        return $row;
    }

    /** @return array<string, mixed> */
    private static function decodeMeta(mixed $value): array
    {
        if (!is_string($value) || $value === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}
