<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Services\ContactService;
use App\Services\PortfolioService;

final class ContactController
{
    public function index(): void
    {
        if (!PortfolioService::installed()) {
            View::render('public/setup', [], 'layouts/bare');
            return;
        }
        $data = PortfolioService::compose('contact');
        $name = (string) ($data['profile']['display_name'] ?? '');
        $data['title'] = setting($data['settings'], 'label_contact', 'Contact') . ' — ' . $name;
        $data['description'] = setting($data['settings'], 'meta_description');
        $data['canonical'] = url('/contact');
        $data['form_errors'] = Session::pull('errors', []);
        $data['form_old'] = Session::pull('old', []);
        $data['form_success'] = Session::pull('success');
        $data['form_error'] = Session::pull('error');
        View::render('public/contact', $data);
    }

    public function send(): void
    {
        if (!CSRF::check(is_string($_POST['_token'] ?? null) ? $_POST['_token'] : null)) {
            $this->fail(419, 'The form expired. Refresh and try again.');
        }
        $input = [
            'name' => Request::line('name', 120),
            'email' => Request::line('email', 190),
            'subject' => Request::line('subject', 190),
            'message' => Request::string('message', 5000),
            'company_website' => trim((string) ($_POST['company_website'] ?? '')),
        ];
        $result = ContactService::submit($input);
        if (!$result['ok']) {
            $this->fail((int) ($result['status'] ?? 422), (string) $result['message'], $result['errors'] ?? [], $input);
        }
        if (wants_json()) {
            json_out(['ok' => true, 'message' => $result['message']]);
        }
        Session::flash('success', $result['message']);
        redirect('/contact');
    }

    /** @param array<string, string> $errors
     *  @param array<string, string> $old
     */
    private function fail(int $status, string $message, array $errors = [], array $old = []): never
    {
        if (wants_json()) {
            json_out(['ok' => false, 'message' => $message, 'errors' => $errors], $status);
        }
        Session::flash('error', $message);
        Session::flash('errors', $errors);
        unset($old['company_website']);
        Session::flash('old', $old);
        redirect('/contact');
    }
}
