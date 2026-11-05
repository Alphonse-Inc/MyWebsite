<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\PortfolioService;

final class HomeController
{
    public function index(): void
    {
        if (!PortfolioService::installed()) {
            View::render('public/setup', [], 'layouts/bare');
            return;
        }
        $data = PortfolioService::compose('home');
        $settings = $data['settings'];
        $data['title'] = (string) $settings['site_title'];
        $data['description'] = (string) $settings['meta_description'];
        $data['canonical'] = url('/');
        $data['jsonLd'] = person_schema($data);
        View::render('public/home', $data);
    }
}
