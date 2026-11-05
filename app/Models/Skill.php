<?php

declare(strict_types=1);

namespace App\Models;

final class Skill extends Model
{
    public static function grouped(bool $public = false): array
    {
        $groups = self::rows('SELECT * FROM skill_groups ORDER BY sort_order ASC, id ASC');
        $skills = self::rows('SELECT * FROM skills ORDER BY sort_order ASC, id ASC');
        $byGroup = [];
        foreach ($skills as $skill) {
            $byGroup[(int) $skill['group_id']][] = $skill;
        }
        foreach ($groups as &$group) {
            $group['skills'] = $byGroup[(int) $group['id']] ?? [];
        }
        unset($group);
        return $groups;
    }

    public static function findGroup(int $id): ?array
    {
        return self::one('SELECT * FROM skill_groups WHERE id = ?', [$id]);
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM skills WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function createGroup(array $data): int
    {
        return self::insert(
            'INSERT INTO skill_groups (name, display_type, sort_order) VALUES (?, ?, ?)',
            [$data['name'], $data['display_type'], $data['sort_order']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function updateGroup(int $id, array $data): void
    {
        self::exec(
            'UPDATE skill_groups SET name = ?, display_type = ?, sort_order = ? WHERE id = ?',
            [$data['name'], $data['display_type'], $data['sort_order'], $id]
        );
    }

    public static function deleteGroup(int $id): void
    {
        self::exec('DELETE FROM skill_groups WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO skills (group_id, name, level, sort_order) VALUES (?, ?, ?, ?)',
            [$data['group_id'], $data['name'], $data['level'], $data['sort_order']]
        );
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        self::exec(
            'UPDATE skills SET group_id = ?, name = ?, level = ?, sort_order = ? WHERE id = ?',
            [$data['group_id'], $data['name'], $data['level'], $data['sort_order'], $id]
        );
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM skills WHERE id = ?', [$id]);
    }
}
