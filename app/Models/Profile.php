<?php

declare(strict_types=1);

namespace App\Models;

final class Profile extends Model
{
    public static function get(): ?array
    {
        $row = self::one('SELECT * FROM profile WHERE id = 1');
        if ($row === null) {
            return null;
        }
        $row['titles'] = self::jsonList($row['titles'] ?? '[]');
        $row['photo_meta'] = self::jsonObject($row['photo_meta'] ?? null);
        $row['hero_poster_meta'] = self::jsonObject($row['hero_poster_meta'] ?? null);
        return $row;
    }

    /** @param array<string, mixed> $data */
    public static function save(array $data): void
    {
        $now = date('Y-m-d H:i:s');
        $existing = self::one('SELECT id FROM profile WHERE id = 1');
        $params = [
            $data['full_name'],
            $data['display_name'],
            $data['handle'],
            json_encode($data['titles'], JSON_UNESCAPED_UNICODE),
            $data['tagline'],
            $data['bio'],
            $data['photo_path'],
            $data['photo_alt'],
            $data['photo_meta'] !== null ? json_encode($data['photo_meta']) : null,
            $data['hero_video_path'],
            $data['hero_poster_path'],
            $data['hero_poster_meta'] !== null ? json_encode($data['hero_poster_meta']) : null,
            $data['availability_label'],
            $data['available'],
            $data['quote'],
            $data['quote_author'],
            $now,
        ];
        if ($existing === null) {
            self::exec(
                'INSERT INTO profile (id, full_name, display_name, handle, titles, tagline, bio, photo_path, photo_alt, photo_meta, hero_video_path, hero_poster_path, hero_poster_meta, availability_label, available, quote, quote_author, updated_at)
                 VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $params
            );
            return;
        }
        self::exec(
            'UPDATE profile SET full_name = ?, display_name = ?, handle = ?, titles = ?, tagline = ?, bio = ?, photo_path = ?, photo_alt = ?, photo_meta = ?, hero_video_path = ?, hero_poster_path = ?, hero_poster_meta = ?, availability_label = ?, available = ?, quote = ?, quote_author = ?, updated_at = ? WHERE id = 1',
            $params
        );
    }

    /** @return list<string> */
    private static function jsonList(mixed $value): array
    {
        if (!is_string($value) || $value === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $item) {
            if (is_string($item) && $item !== '') {
                $out[] = $item;
            }
        }
        return $out;
    }

    /** @return array<string, mixed>|null */
    private static function jsonObject(mixed $value): ?array
    {
        if (!is_string($value) || $value === '') {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }
}
