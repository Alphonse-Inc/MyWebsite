<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\LoginAttempt;
use App\Models\User;
use PDOException;

final class Auth
{
    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!is_int($id) && !is_string($id)) {
            return null;
        }
        try {
            return User::find((int) $id);
        } catch (PDOException) {
            return null;
        }
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(string $email, string $password): string
    {
        $email = strtolower(trim($email));
        $ip = client_ip();
        if (LoginAttempt::tooMany($email, $ip)) {
            return 'locked';
        }
        $user = User::findByEmail($email);
        $valid = $user !== null && password_verify($password, (string) $user['password_hash']);
        LoginAttempt::record($email, $ip, $valid);
        if (!$valid || $user === null) {
            return 'invalid';
        }
        if ((int) $user['totp_enabled'] === 1 && is_string($user['totp_secret']) && $user['totp_secret'] !== '') {
            Session::set('pending_uid', (int) $user['id']);
            Session::set('pending_until', time() + 300);
            return 'totp';
        }
        self::login($user);
        return 'ok';
    }

    public static function completeTotp(string $code): bool
    {
        $id = Session::get('pending_uid');
        $until = Session::get('pending_until');
        if (!is_numeric($id) || !is_numeric($until) || time() > (int) $until) {
            return false;
        }
        $user = User::find((int) $id);
        if ($user === null || (int) $user['totp_enabled'] !== 1) {
            return false;
        }
        if (!Totp::verify((string) $user['totp_secret'], $code)) {
            LoginAttempt::record((string) $user['email'], client_ip(), false);
            return false;
        }
        Session::forget('pending_uid');
        Session::forget('pending_until');
        self::login($user);
        return true;
    }

    /** @param array<string, mixed> $user */
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('last_activity', time());
        User::touchLogin((int) $user['id']);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function requireUser(): void
    {
        $user = self::user();
        if ($user === null) {
            redirect('/admin/login');
        }
        $idle = (int) (config('app')['session_idle'] ?? 1800);
        $last = Session::get('last_activity');
        if (is_numeric($last) && (time() - (int) $last) > $idle) {
            self::logout();
            Session::start();
            Session::flash('error', 'You were signed out after a period of inactivity.');
            redirect('/admin/login');
        }
        Session::set('last_activity', time());
        $path = Request::path();
        $path = rtrim($path, '/') ?: '/';
        $allowed = ['/admin/password', '/admin/logout'];
        if ((int) $user['must_change_password'] === 1 && !in_array($path, $allowed, true)) {
            redirect('/admin/password');
        }
    }
}
