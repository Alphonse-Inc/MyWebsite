<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\BackupService;

final class BackupController extends AdminController
{
    public function index(): void
    {
        $this->admin('admin/backup', ['pageTitle' => 'Backup']);
    }

    public function create(): void
    {
        $this->guardPost();
        BackupService::download();
    }
}
