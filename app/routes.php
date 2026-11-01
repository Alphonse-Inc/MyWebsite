<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\CvController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\ResumeController;
use App\Controllers\SeoController;
use App\Controllers\Admin\AccountController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\BackupController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MessageController;
use App\Controllers\Admin\ProjectController as AdminProjectController;
use App\Controllers\Admin\SettingsController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/projects', [ProjectController::class, 'index']);
    $router->get('/projects/{slug}', [ProjectController::class, 'show']);
    $router->get('/resume', [ResumeController::class, 'index']);
    $router->get('/contact', [ContactController::class, 'index']);
    $router->post('/contact', [ContactController::class, 'send']);
    $router->get('/cv', [CvController::class, 'download']);
    $router->get('/sitemap.xml', [SeoController::class, 'sitemap']);
    $router->get('/robots.txt', [SeoController::class, 'robots']);

    $router->get('/admin/login', [AuthController::class, 'loginForm']);
    $router->post('/admin/login', [AuthController::class, 'login']);
    $router->get('/admin/login/totp', [AuthController::class, 'totpForm']);
    $router->post('/admin/login/totp', [AuthController::class, 'totp']);
    $router->post('/admin/logout', [AuthController::class, 'logout']);
    $router->get('/admin/forgot-password', [AuthController::class, 'forgotForm']);
    $router->post('/admin/forgot-password', [AuthController::class, 'forgot']);
    $router->get('/admin/reset-password/{token}', [AuthController::class, 'resetForm']);
    $router->post('/admin/reset-password/{token}', [AuthController::class, 'reset']);

    $router->get('/admin', [DashboardController::class, 'index']);
    $router->get('/admin/password', [AccountController::class, 'passwordForm']);
    $router->post('/admin/password', [AccountController::class, 'passwordSave']);
    $router->get('/admin/security', [AccountController::class, 'security']);
    $router->post('/admin/security/totp', [AccountController::class, 'totpStart']);
    $router->post('/admin/security/totp/confirm', [AccountController::class, 'totpConfirm']);
    $router->post('/admin/security/totp/disable', [AccountController::class, 'totpDisable']);

    $router->get('/admin/projects', [AdminProjectController::class, 'index']);
    $router->get('/admin/projects/new', [AdminProjectController::class, 'createForm']);
    $router->post('/admin/projects', [AdminProjectController::class, 'store']);
    $router->post('/admin/projects/reorder', [AdminProjectController::class, 'reorder']);
    $router->post('/admin/projects/bulk-delete', [AdminProjectController::class, 'bulkDelete']);
    $router->get('/admin/projects/{id}/edit', [AdminProjectController::class, 'editForm']);
    $router->post('/admin/projects/{id}', [AdminProjectController::class, 'update']);
    $router->post('/admin/projects/{id}/delete', [AdminProjectController::class, 'destroy']);
    $router->post('/admin/projects/{id}/toggle', [AdminProjectController::class, 'toggle']);
    $router->post('/admin/projects/{id}/duplicate', [AdminProjectController::class, 'duplicate']);
    $router->post('/admin/projects/{id}/images', [AdminProjectController::class, 'upload']);
    $router->post('/admin/projects/{id}/images/reorder', [AdminProjectController::class, 'reorderImages']);
    $router->post('/admin/projects/{id}/images/{imageId}/cover', [AdminProjectController::class, 'cover']);
    $router->post('/admin/projects/{id}/images/{imageId}/alt', [AdminProjectController::class, 'alt']);
    $router->post('/admin/projects/{id}/images/{imageId}/delete', [AdminProjectController::class, 'deleteImage']);

    $router->get('/admin/profile', [ContentController::class, 'profileForm']);
    $router->post('/admin/profile', [ContentController::class, 'profileSave']);
    $router->get('/admin/services', [ContentController::class, 'services']);
    $router->post('/admin/services', [ContentController::class, 'serviceSave']);
    $router->post('/admin/services/reorder', [ContentController::class, 'serviceReorder']);
    $router->post('/admin/services/{id}/delete', [ContentController::class, 'serviceDelete']);
    $router->get('/admin/experience', [ContentController::class, 'experience']);
    $router->post('/admin/experience', [ContentController::class, 'experienceSave']);
    $router->post('/admin/experience/{id}/delete', [ContentController::class, 'experienceDelete']);
    $router->get('/admin/education', [ContentController::class, 'education']);
    $router->post('/admin/education', [ContentController::class, 'educationSave']);
    $router->post('/admin/education/{id}/delete', [ContentController::class, 'educationDelete']);
    $router->get('/admin/skills', [ContentController::class, 'skills']);
    $router->post('/admin/skills/groups', [ContentController::class, 'skillGroupSave']);
    $router->post('/admin/skills/groups/{id}/delete', [ContentController::class, 'skillGroupDelete']);
    $router->post('/admin/skills', [ContentController::class, 'skillSave']);
    $router->post('/admin/skills/{id}/delete', [ContentController::class, 'skillDelete']);
    $router->get('/admin/socials', [ContentController::class, 'socials']);
    $router->post('/admin/socials', [ContentController::class, 'socialSave']);
    $router->post('/admin/socials/{id}/delete', [ContentController::class, 'socialDelete']);
    $router->get('/admin/testimonials', [ContentController::class, 'testimonials']);
    $router->post('/admin/testimonials', [ContentController::class, 'testimonialSave']);
    $router->post('/admin/testimonials/{id}/delete', [ContentController::class, 'testimonialDelete']);
    $router->get('/admin/contact', [ContentController::class, 'contactForm']);
    $router->post('/admin/contact', [ContentController::class, 'contactSave']);
    $router->get('/admin/cv', [ContentController::class, 'cvForm']);
    $router->post('/admin/cv', [ContentController::class, 'cvSave']);
    $router->post('/admin/cv/delete', [ContentController::class, 'cvDelete']);

    $router->get('/admin/settings', [SettingsController::class, 'edit']);
    $router->post('/admin/settings', [SettingsController::class, 'save']);
    $router->get('/admin/messages', [MessageController::class, 'index']);
    $router->get('/admin/messages/export', [MessageController::class, 'export']);
    $router->get('/admin/messages/{id}', [MessageController::class, 'show']);
    $router->post('/admin/messages/{id}/status', [MessageController::class, 'status']);
    $router->post('/admin/messages/{id}/reply', [MessageController::class, 'reply']);
    $router->post('/admin/messages/{id}/delete', [MessageController::class, 'delete']);
    $router->get('/admin/backup', [BackupController::class, 'index']);
    $router->post('/admin/backup', [BackupController::class, 'create']);
};
