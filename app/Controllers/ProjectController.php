<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Project;
use App\Services\PortfolioService;

final class ProjectController
{
    public function index(): void
    {
        $this->guardInstalled();
        $data = PortfolioService::compose('projects');
        $name = (string) ($data['profile']['display_name'] ?? 'Portfolio');
        $data['title'] = setting($data['settings'], 'label_projects', 'Projects') . ' — ' . $name;
        $data['description'] = setting($data['settings'], 'meta_description');
        $data['canonical'] = url('/projects');
        View::render('public/projects/index', $data);
    }

    public function show(string $slug): void
    {
        $this->guardInstalled();
        $project = Project::findBySlug($slug, true);
        if ($project === null) {
            http_response_code(404);
            $data = PortfolioService::compose('missing');
            $data['title'] = setting($data['settings'], 'error_404_title', 'Page not found');
            View::render('errors/404', $data);
            return;
        }
        $data = PortfolioService::compose('projects');
        $data['project'] = $project;
        $data['images'] = Project::images((int) $project['id']);
        $data['neighbors'] = Project::neighbors((int) $project['id']);
        $name = (string) ($data['profile']['display_name'] ?? '');
        $data['title'] = $project['name'] . ' — ' . $name;
        $data['description'] = (string) $project['short_description'];
        $data['canonical'] = url('/projects/' . $project['slug']);
        $data['jsonLd'] = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project['name'],
            'url' => $project['url'],
            'description' => $project['short_description'],
            'author' => [
                '@type' => 'Person',
                'name' => $data['profile']['full_name'] ?? '',
            ],
        ];
        View::render('public/projects/show', $data);
    }

    private function guardInstalled(): void
    {
        if (!PortfolioService::installed()) {
            View::render('public/setup', [], 'layouts/bare');
            exit;
        }
    }
}
