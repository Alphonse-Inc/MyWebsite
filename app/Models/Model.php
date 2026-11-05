<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected static function pdo(): PDO
    {
        return Database::pdo();
    }

    /** @param array<int|string, mixed> $params */
    protected static function rows(string $sql, array $params = []): array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** @param array<int|string, mixed> $params */
    protected static function one(string $sql, array $params = []): ?array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** @param array<int|string, mixed> $params */
    protected static function exec(string $sql, array $params = []): int
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** @param array<int|string, mixed> $params */
    protected static function insert(string $sql, array $params = []): int
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return (int) self::pdo()->lastInsertId();
    }
}
