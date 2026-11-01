<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\PasswordReset;
use App\Models\RateLimit;
use App\Models\User;

final class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        $this->authView('admin/auth/login', 'Sign in');
    }

    public function login(): void
    {
        CSRF::requireValid();
        $email = Request::line('email', 190);
        $password = (string) ($_POST['password'] ?? '');
        $result = Auth::attempt($email, $password);
        if ($result === 'locked') {
            Session::flash('error', 'Too many attempts. Try again in 15 minutes.');
            redirect('/admin/login');
        }
        if ($result === 'invalid') {
            Session::flash('error', 'Those details do not match an account.');
            Session::flash('old', ['email' => $email]);
            redirect('/admin/login');
        }
        if ($result === 'totp') {
            redirect('/admin/login/totp');
        }
        redirect('/admin');
    }

    public function totpForm(): void
    {
        if (!is_numeric(Session::get('pending_uid'))) {
            redirect('/admin/login');
        }
        $this->authView('admin/auth/totp', 'Authenticator code');
    }

    public function totp(): void
    {
        CSRF::requireValid();
        $code = Request::line('code', 12);
        if (!Auth::completeTotp($code)) {
            Session::flash('error', 'That code is not valid.');
            redirect('/admin/login/totp');
        }
        redirect('/admin');
    }

    public function logout(): void
    {
        CSRF::requireValid();
        Auth::logout();
        redirect('/admin/login');
    }

    public function forgotForm(): void
    {
        $this->authView('admin/auth/forgot', 'Reset password');
    }

    public function forgot(): void
    {
        CSRF::requireValid();
        $email = strtolower(Request::line('email', 190));
        if (!RateLimit::hit('password-reset', client_ip(), 5, 3600)) {
            Session::flash('error', 'Too many reset requests. Try again later.');
            redirect('/admin/forgot-password');
        }
        $user = User::findByEmail($email);
        if ($user !== null) {
            $token = bin2hex(random_bytes(32));
            PasswordReset::create((int) $user['id'], hash('sha256', $token), date('Y-m-d H:i:s', time() + (int) (config('app')['reset_ttl'] ?? 3600)));
            $link = url('/admin/reset-password/' . $token);
            Mailer::send(
                $email,
                'Reset your portfolio password',
                "A password reset was requested. This link expires in one hour:\n\n" . $link . "\n\nIf you did not ask for this, ignore the email.",
                '<p>A password reset was requested. This link expires in one hour:</p><p><a href="' . e($link) . '">' . e($link) . '</a></p>'
            );
        }
        Session::flash('success', 'If that account exists, a reset link is on its way.');
        redirect('/admin/forgot-password');
    }

    public function resetForm(string $token): void
    {
        $row = PasswordReset::findValid(hash('sha256', $token));
        if ($row === null) {
            Session::flash('error', 'That reset link is invalid or expired.');
            redirect('/admin/forgot-password');
        }
        $this->authView('admin/auth/reset', 'Choose a new password', ['token' => $token]);
    }

    public function reset(string $token): void
    {
        CSRF::requireValid();
        $row = PasswordReset::findValid(hash('sha256', $token));
        if ($row === null) {
            Session::flash('error', 'That reset link is invalid or expired.');
            redirect('/admin/forgot-password');
        }
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');
        if (!$this->strong($password) || $password !== $confirm) {
            Session::flash('error', 'Use 12 or more characters, with letters and numbers, and make both fields match.');
            redirect('/admin/reset-password/' . $token);
        }
        User::updatePassword((int) $row['user_id'], password_hash($password, PASSWORD_DEFAULT));
        PasswordReset::markUsed((int) $row['id']);
        Session::flash('success', 'Password updated. Sign in with the new password.');
        redirect('/admin/login');
    }

    /** @param array<string, mixed> $data */
    private function authView(string $view, string $title, array $data = []): void
    {
        $data['pageTitle'] = $title;
        $data['error'] = Session::pull('error');
        $data['success'] = Session::pull('success');
        $data['old'] = Session::pull('old', []);
        View::render($view, $data, 'layouts/bare');
    }

    private function strong(string $password): bool
    {
        return strlen($password) >= 12
            && preg_match('/[A-Za-z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }
}
