<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);
        $save = dirname(__DIR__, 2) . '/storage/sessions';
        if (!is_dir($save)) {
            mkdir($save, 0700, true);
        }
        session_save_path($save);
        session_name((string) (config('app')['session_name'] ?? 'ea_portfolio'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        if (isset($_SESSION['_flash_next']) && is_array($_SESSION['_flash_next'])) {
            $_SESSION['_flash'] = $_SESSION['_flash_next'];
            unset($_SESSION['_flash_next']);
        } else {
            $_SESSION['_flash'] = [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        if (!isset($_SESSION['_flash_next']) || !is_array($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
    }
}
