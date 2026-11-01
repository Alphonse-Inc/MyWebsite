<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Mailer;
use App\Models\Message;
use App\Models\RateLimit;
use App\Models\Setting;

final class ContactService
{
    /** @param array<string, string> $input */
    public static function submit(array $input): array
    {
        if (($input['company_website'] ?? '') !== '') {
            return ['ok' => true, 'message' => Setting::get('contact_success', 'Thanks, your message is sent successfully.')];
        }
        $limit = (int) (config('app')['contact_max_per_hour'] ?? 5);
        if (!RateLimit::hit('contact', client_ip(), $limit, 3600)) {
            return ['ok' => false, 'status' => 429, 'message' => 'Too many messages from this network. Please try again later.'];
        }
        $errors = self::validate($input);
        if ($errors !== []) {
            return ['ok' => false, 'status' => 422, 'message' => 'Check the highlighted fields.', 'errors' => $errors];
        }
        Message::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'subject' => $input['subject'],
            'body' => $input['message'],
            'ip' => client_ip(),
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            'status' => 'unread',
        ]);
        $settings = Setting::all();
        $notify = $settings['notify_email'] ?? '';
        $text = "Name: {$input['name']}\nEmail: {$input['email']}\nSubject: {$input['subject']}\n\n{$input['message']}";
        $html = '<p><strong>Name:</strong> ' . e($input['name']) . '<br><strong>Email:</strong> ' . e($input['email']) . '<br><strong>Subject:</strong> ' . e($input['subject']) . '</p><p>' . nl2br(e($input['message'])) . '</p>';
        if ($notify !== '') {
            Mailer::send($notify, 'Portfolio message: ' . $input['subject'], $text, $html);
        }
        if (($settings['auto_reply_enabled'] ?? '0') === '1') {
            $reply = (string) ($settings['auto_reply_body'] ?? '');
            Mailer::send(
                $input['email'],
                (string) ($settings['auto_reply_subject'] ?? 'Thanks for your message'),
                $reply,
                '<p>' . nl2br(e($reply)) . '</p>'
            );
        }
        return ['ok' => true, 'message' => $settings['contact_success'] ?? 'Thanks, your message is sent successfully.'];
    }

    /** @param array<string, string> $input
     *  @return array<string, string>
     */
    private static function validate(array $input): array
    {
        $errors = [];
        if ($input['name'] === '' || mb_strlen($input['name']) > 120) {
            $errors['name'] = 'Name is required (120 characters max).';
        }
        if (filter_var($input['email'], FILTER_VALIDATE_EMAIL) === false || mb_strlen($input['email']) > 190) {
            $errors['email'] = 'A valid email is required.';
        }
        if ($input['subject'] === '' || mb_strlen($input['subject']) > 190) {
            $errors['subject'] = 'Subject is required (190 characters max).';
        }
        if ($input['message'] === '' || mb_strlen($input['message']) > 5000) {
            $errors['message'] = 'Message is required (5000 characters max).';
        }
        return $errors;
    }
}
