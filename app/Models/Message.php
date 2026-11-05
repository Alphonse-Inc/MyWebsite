<?php

declare(strict_types=1);

namespace App\Models;

final class Message extends Model
{
    /** @param array<string, string> $filters */
    public static function search(array $filters = []): array
    {
        $sql = 'SELECT * FROM messages WHERE 1 = 1';
        $params = [];
        if (($filters['q'] ?? '') !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR body LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params = [$like, $like, $like, $like];
        }
        if (($filters['status'] ?? '') !== '') {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        $sql .= ' ORDER BY created_at DESC, id DESC';
        return self::rows($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return self::one('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return self::insert(
            'INSERT INTO messages (name, email, subject, body, ip, user_agent, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$data['name'], $data['email'], $data['subject'], $data['body'], $data['ip'], $data['user_agent'], $data['status'], date('Y-m-d H:i:s')]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        self::exec('UPDATE messages SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function delete(int $id): void
    {
        self::exec('DELETE FROM messages WHERE id = ?', [$id]);
    }

    public static function counts(): array
    {
        $rows = self::rows('SELECT status, COUNT(*) AS total FROM messages GROUP BY status');
        $out = ['unread' => 0, 'read' => 0, 'spam' => 0, 'replied' => 0, 'all' => 0];
        foreach ($rows as $row) {
            $out[(string) $row['status']] = (int) $row['total'];
            $out['all'] += (int) $row['total'];
        }
        return $out;
    }

    public static function latest(int $limit = 5): array
    {
        return self::rows('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT ' . max(1, min(20, $limit)));
    }
}
