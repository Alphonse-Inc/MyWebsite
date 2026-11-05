<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Cache;
use App\Core\CSRF;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;

abstract class AdminController
{
    public function __construct()
    {
        Auth::requireUser();
    }

    protected function guardPost(): void
    {
        if (Request::method() !== 'POST') {
            http_response_code(405);
            exit;
        }
        CSRF::requireValid();
    }

    protected function saved(string $message, string $to): never
    {
        Cache::flush();
        Session::flash('success', $message);
        redirect($to);
    }

    /** @param array<string, mixed> $data */
    protected function admin(string $view, array $data = []): void
    {
        $data['adminUser'] = Auth::user();
        $data['success'] = Session::pull('success');
        $data['error'] = Session::pull('error');
        $data['errors'] = Session::pull('errors', []);
        $data['old'] = Session::pull('old', []);
        $data['pageTitle'] = $data['pageTitle'] ?? 'Admin';
        View::render($view, $data, 'layouts/admin');
    }

    /** @param array<string, string> $errors
     *  @param array<string, mixed> $old
     */
    protected function backWithErrors(string $to, array $errors, array $old): never
    {
        Session::flash('errors', $errors);
        Session::flash('old', $old);
        Session::flash('error', 'Fix the highlighted fields.');
        redirect($to);
    }
}
