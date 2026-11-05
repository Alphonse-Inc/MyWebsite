<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Setting;
use App\Services\Media;
use App\Services\PortfolioService;

final class CvController
{
    public function download(): void
    {
        if (!PortfolioService::installed()) {
            View::render('public/setup', [], 'layouts/bare');
            return;
        }
        $relative = Setting::get('cv_path', '');
        $path = $relative !== '' ? Media::root() . '/' . ltrim($relative, '/') : '';
        if ($path === '' || !is_file($path)) {
            http_response_code(404);
            $data = PortfolioService::compose('missing');
            $data['title'] = 'CV unavailable';
            $data['cvMissing'] = true;
            View::render('errors/404', $data);
            return;
        }
        Setting::increment('cv_downloads');
        $name = 'Emmanuel-Afolayan-CV.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        readfile($path);
        exit;
    }
}
