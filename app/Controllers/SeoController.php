<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Project;
use App\Services\PortfolioService;

final class SeoController
{
    public function sitemap(): void
    {
        $base = app_url();
        $urls = [
            ['loc' => $base . '/', 'priority' => '1.0'],
            ['loc' => $base . '/projects', 'priority' => '0.8'],
            ['loc' => $base . '/resume', 'priority' => '0.7'],
            ['loc' => $base . '/contact', 'priority' => '0.6'],
        ];
        if (PortfolioService::installed()) {
            foreach (Project::published() as $project) {
                $urls[] = ['loc' => $base . '/projects/' . $project['slug'], 'priority' => '0.6'];
            }
        }
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url><loc>' . e($url['loc']) . '</loc><priority>' . e($url['priority']) . '</priority></url>';
        }
        echo '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo 'Sitemap: ' . url('/sitemap.xml') . "\n";
    }
}
