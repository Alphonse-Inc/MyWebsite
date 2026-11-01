<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Message;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;

final class DashboardController extends AdminController
{
    public function index(): void
    {
        $counts = Message::counts();
        $this->admin('admin/dashboard', [
            'pageTitle' => 'Dashboard',
            'messages' => $counts,
            'latest' => Message::latest(5),
            'projects' => Project::countPublished(),
            'services' => count(Service::allRows()),
            'downloads' => (int) Setting::get('cv_downloads', '0'),
        ]);
    }
}
