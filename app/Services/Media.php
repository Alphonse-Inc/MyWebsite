<?php

declare(strict_types=1);

namespace App\Services;

final class Media
{
    public static function root(): string
    {
        return project_root() . '/public_html/uploads';
    }

    public static function projectDir(int $id): string
    {
        return self::root() . '/projects/' . $id;
    }

    public static function baseUrl(int $projectId, string $stem): string
    {
        return '/uploads/projects/' . $projectId . '/' . $stem;
    }

    public static function profileUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }
        return '/uploads/' . ltrim($path, '/');
    }
}
