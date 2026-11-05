<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\PortfolioService;

final class ResumeController
{
    public function index(): void
    {
        if (!PortfolioService::installed()) {
            View::render('public/setup', [], 'layouts/bare');
            return;
        }
        $data = PortfolioService::compose('resume');
        $name = (string) ($data['profile']['display_name'] ?? 'Resume');
        $data['title'] = setting($data['settings'], 'label_resume', 'Resume') . ' — ' . $name;
        $data['description'] = (string) ($data['profile']['bio'] ?? setting($data['settings'], 'meta_description'));
        $data['canonical'] = url('/resume');
        $data['jsonLd'] = person_schema($data);
        View::render('public/resume', $data);
    }
}
