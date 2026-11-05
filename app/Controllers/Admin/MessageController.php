<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Mailer;
use App\Core\Request;
use App\Models\Message;

final class MessageController extends AdminController
{
    public function index(): void
    {
        $filters = [
            'q' => Request::line('q', 120),
            'status' => Request::line('status', 20),
        ];
        if (!in_array($filters['status'], ['', 'unread', 'read', 'spam', 'replied'], true)) {
            $filters['status'] = '';
        }
        $this->admin('admin/messages/index', [
            'pageTitle' => 'Messages',
            'messages' => Message::search($filters),
            'filters' => $filters,
            'counts' => Message::counts(),
        ]);
    }

    public function show(string $id): void
    {
        $message = Message::find((int) $id);
        if ($message === null) {
            http_response_code(404);
            exit('Message not found.');
        }
        if ($message['status'] === 'unread') {
            Message::setStatus((int) $message['id'], 'read');
            $message['status'] = 'read';
        }
        $this->admin('admin/messages/show', [
            'pageTitle' => 'Message',
            'message' => $message,
        ]);
    }

    public function status(string $id): void
    {
        $this->guardPost();
        $status = Request::line('status', 20);
        if (!in_array($status, ['unread', 'read', 'spam', 'replied'], true)) {
            $this->backWithErrors('/admin/messages/' . (int) $id, ['status' => 'Unknown status.'], []);
        }
        Message::setStatus((int) $id, $status);
        $this->saved('Message updated.', '/admin/messages/' . (int) $id);
    }

    public function reply(string $id): void
    {
        $this->guardPost();
        $message = Message::find((int) $id);
        if ($message === null) {
            http_response_code(404);
            exit('Message not found.');
        }
        $body = Request::string('reply', 5000);
        if ($body === '') {
            $this->backWithErrors('/admin/messages/' . $message['id'], ['reply' => 'Write a reply first.'], []);
        }
        $sent = Mailer::send(
            (string) $message['email'],
            'Re: ' . (string) $message['subject'],
            $body,
            '<p>' . nl2br(e($body)) . '</p>'
        );
        if (!$sent) {
            $this->backWithErrors('/admin/messages/' . $message['id'], ['reply' => 'The email could not be sent. Check SMTP settings. The message is still in the inbox.'], ['reply' => $body]);
        }
        Message::setStatus((int) $message['id'], 'replied');
        $this->saved('Reply sent.', '/admin/messages/' . $message['id']);
    }

    public function delete(string $id): void
    {
        $this->guardPost();
        Message::delete((int) $id);
        $this->saved('Message deleted.', '/admin/messages');
    }

    public function export(): void
    {
        $rows = Message::search([]);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="messages.csv"');
        $out = fopen('php://output', 'w');
        if ($out === false) {
            exit;
        }
        fputcsv($out, ['id', 'created_at', 'status', 'name', 'email', 'subject', 'body', 'ip']);
        foreach ($rows as $row) {
            fputcsv($out, [$row['id'], $row['created_at'], $row['status'], $row['name'], $row['email'], $row['subject'], $row['body'], $row['ip']]);
        }
        fclose($out);
        exit;
    }
}
