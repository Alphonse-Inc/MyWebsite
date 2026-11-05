<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function send(string $to, string $subject, string $text, ?string $html = null): bool
    {
        $to = trim($to);
        if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        $settings = Setting::all();
        $mail = new PHPMailer(true);
        try {
            $host = trim((string) ($settings['smtp_host'] ?? ''));
            $usingEnv = false;
            if ($host === '') {
                $host = trim((string) Env::get('SMTP_HOST', ''));
                $usingEnv = $host !== '';
            }
            if ($host !== '') {
                $mail->isSMTP();
                $mail->Host = $host;
                if ($usingEnv) {
                    $mail->Port = (int) Env::get('SMTP_PORT', '587');
                    $user = (string) Env::get('SMTP_USER', '');
                    $pass = (string) Env::get('SMTP_PASS', '');
                    $encryption = strtolower((string) Env::get('SMTP_ENCRYPTION', 'tls'));
                } else {
                    $mail->Port = (int) ($settings['smtp_port'] ?? 587);
                    $user = (string) ($settings['smtp_user'] ?? '');
                    $pass = (string) ($settings['smtp_pass'] ?? '');
                    $encryption = strtolower((string) ($settings['smtp_encryption'] ?? 'tls'));
                }
                $mail->SMTPAuth = $user !== '';
                $mail->Username = $user;
                $mail->Password = $pass;
                if ($encryption === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } elseif ($encryption === 'ssl') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;
                }
            } else {
                $mail->isMail();
            }
            $fromEmail = (string) ($settings['smtp_from_email'] ?? '');
            if ($fromEmail === '') {
                $fromEmail = (string) Env::get('SMTP_FROM_EMAIL', (string) ($settings['notify_email'] ?? 'iamemmaafolayan@gmail.com'));
            }
            $fromName = (string) ($settings['smtp_from_name'] ?? '');
            if ($fromName === '') {
                $fromName = (string) Env::get('SMTP_FROM_NAME', 'Emmanuel Afolayan');
            }
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->Subject = single_line($subject, 180);
            $mail->CharSet = 'UTF-8';
            if ($html !== null && $html !== '') {
                $mail->isHTML(true);
                $mail->Body = $html;
                $mail->AltBody = $text;
            } else {
                $mail->Body = $text;
            }
            return $mail->send();
        } catch (Exception $e) {
            self::log($e->getMessage());
            return false;
        } catch (\Throwable $e) {
            self::log($e->getMessage());
            return false;
        }
    }

    private static function log(string $message): void
    {
        $dir = project_root() . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . '/mail.log', date('c') . ' ' . $message . "\n", FILE_APPEND);
    }
}
