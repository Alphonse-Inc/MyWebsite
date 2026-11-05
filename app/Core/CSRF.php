<?php

declare(strict_types=1);

namespace App\Core;

final class CSRF
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!is_string($token) || strlen($token) < 32) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf', $token);
        }
        return $token;
    }

    public static function check(?string $token): bool
    {
        $known = Session::get('_csrf');
        return is_string($known) && is_string($token) && $token !== '' && hash_equals($known, $token);
    }

    public static function requireValid(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!self::check(is_string($token) ? $token : null)) {
            http_response_code(419);
            if (wants_json()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'message' => 'The form expired. Refresh and try again.']);
                exit;
            }
            Session::flash('error', 'The form expired. Refresh and try again.');
            $back = $_SERVER['HTTP_REFERER'] ?? url('/admin');
            header('Location: ' . $back);
            exit;
        }
    }
}
