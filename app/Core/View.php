<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $template, array $data = [], string $layout = 'layouts/public'): void
    {
        $data['contentTemplate'] = $template;
        $viewFile = self::path($template);
        $layoutFile = self::path($layout);
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }

    /** @param array<string, mixed> $data */
    public static function partial(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require self::path($template);
    }

    public static function path(string $template): string
    {
        $file = dirname(__DIR__) . '/Views/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('View not found: ' . $template);
        }
        return $file;
    }
}
