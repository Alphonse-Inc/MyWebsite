<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Totp;
use App\Models\User;

final class AccountController extends AdminController
{
    public function passwordForm(): void
    {
        $user = Auth::user();
        $this->admin('admin/password', [
            'pageTitle' => 'Password',
            'mustChange' => $user !== null && (int) $user['must_change_password'] === 1,
        ]);
    }

    public function passwordSave(): void
    {
        $this->guardPost();
        $user = Auth::user();
        if ($user === null) {
            redirect('/admin/login');
        }
        $current = (string) ($_POST['current_password'] ?? '');
        $next = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');
        if (!password_verify($current, (string) $user['password_hash'])) {
            $this->backWithErrors('/admin/password', ['current_password' => 'Current password is wrong.'], []);
        }
        if (strlen($next) < 12 || preg_match('/[A-Za-z]/', $next) !== 1 || preg_match('/\d/', $next) !== 1 || $next !== $confirm) {
            $this->backWithErrors('/admin/password', ['password' => 'Use 12 or more characters with letters and numbers, and repeat it exactly.'], []);
        }
        User::updatePassword((int) $user['id'], password_hash($next, PASSWORD_DEFAULT));
        Session::flash('success', 'Password changed.');
        redirect('/admin');
    }

    public function security(): void
    {
        $user = Auth::user();
        $this->admin('admin/security', [
            'pageTitle' => 'Security',
            'user' => $user,
            'pendingSecret' => Session::get('totp_pending'),
            'pendingUri' => Session::get('totp_uri'),
        ]);
    }

    public function totpStart(): void
    {
        $this->guardPost();
        $user = Auth::user();
        if ($user === null) {
            redirect('/admin/login');
        }
        $secret = Totp::secret();
        Session::set('totp_pending', $secret);
        Session::set('totp_uri', Totp::provisioningUri($secret, (string) $user['email'], 'Emmanuel Afolayan'));
        redirect('/admin/security');
    }

    public function totpConfirm(): void
    {
        $this->guardPost();
        $user = Auth::user();
        $secret = Session::get('totp_pending');
        if ($user === null || !is_string($secret)) {
            redirect('/admin/security');
        }
        if (!Totp::verify($secret, Request::line('code', 12))) {
            $this->backWithErrors('/admin/security', ['code' => 'That authenticator code does not match.'], []);
        }
        User::updateTotp((int) $user['id'], $secret, true);
        Session::forget('totp_pending');
        Session::forget('totp_uri');
        $this->saved('Authenticator app enabled.', '/admin/security');
    }

    public function totpDisable(): void
    {
        $this->guardPost();
        $user = Auth::user();
        if ($user === null) {
            redirect('/admin/login');
        }
        $password = (string) ($_POST['current_password'] ?? '');
        if (!password_verify($password, (string) $user['password_hash'])) {
            $this->backWithErrors('/admin/security', ['current_password' => 'Current password is wrong.'], []);
        }
        User::updateTotp((int) $user['id'], null, false);
        $this->saved('Authenticator app disabled.', '/admin/security');
    }
}
