<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Models\Setting;

final class SettingsController extends AdminController
{
    public function edit(): void
    {
        $this->admin('admin/settings', [
            'pageTitle' => 'Settings',
            'settings' => Setting::all(),
        ]);
    }

    public function save(): void
    {
        $this->guardPost();
        $keys = [
            'site_title', 'meta_description', 'meta_keywords', 'meta_author', 'footer_text', 'analytics_id',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption', 'smtp_from_email', 'smtp_from_name',
            'notify_email', 'auto_reply_subject', 'auto_reply_body',
            'label_about', 'label_services', 'label_resume', 'label_experience', 'label_education', 'label_skills',
            'label_projects', 'label_contact', 'label_quote', 'label_get_in_touch',
            'btn_download_cv', 'btn_contact', 'btn_send', 'btn_visit', 'btn_view_project',
            'form_name', 'form_email', 'form_subject', 'form_message',
            'contact_success', 'contact_error', 'error_404_title', 'error_404_body', 'error_500_title', 'error_500_body',
            'career_start_year', 'incident_remedy_percent',
        ];
        $pairs = [];
        foreach ($keys as $key) {
            $max = in_array($key, ['meta_description', 'auto_reply_body', 'error_404_body', 'error_500_body', 'footer_text'], true) ? 2000 : 300;
            $pairs[$key] = str_contains($key, 'body') || str_contains($key, 'description') && $key !== 'meta_description'
                ? Request::string($key, $max)
                : Request::line($key, $max);
        }
        $pairs['meta_description'] = Request::line('meta_description', 300);
        $pairs['auto_reply_enabled'] = isset($_POST['auto_reply_enabled']) ? '1' : '0';
        if (!in_array($pairs['smtp_encryption'], ['tls', 'ssl', 'none'], true)) {
            $pairs['smtp_encryption'] = 'tls';
        }
        if ($pairs['smtp_pass'] === '') {
            $pairs['smtp_pass'] = Setting::get('smtp_pass', '');
        }
        Setting::putMany($pairs);
        $this->saved('Settings saved.', '/admin/settings');
    }
}
