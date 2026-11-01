<?php
/** @var string $content */
/** @var string $pageTitle */
/** @var array<string, mixed>|null $adminUser */
$adminUser = $adminUser ?? [];
$nav = [
    ['/admin', 'Dashboard', 'panels-top-left'],
    ['/admin/projects', 'Projects', 'image'],
    ['/admin/profile', 'Profile', 'circle-user'],
    ['/admin/services', 'Services', 'code'],
    ['/admin/experience', 'Experience', 'briefcase'],
    ['/admin/education', 'Education', 'graduation-cap'],
    ['/admin/skills', 'Skills', 'sliders-horizontal'],
    ['/admin/socials', 'Socials', 'external-link'],
    ['/admin/contact', 'Contact', 'mail'],
    ['/admin/cv', 'CV', 'file-text'],
    ['/admin/testimonials', 'Testimonials', 'quote'],
    ['/admin/messages', 'Messages', 'inbox'],
    ['/admin/settings', 'Settings', 'settings'],
    ['/admin/backup', 'Backup', 'database'],
    ['/admin/security', 'Security', 'shield'],
    ['/admin/password', 'Password', 'key-round'],
];
$path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= e(\App\Core\CSRF::token()) ?>">
    <meta name="theme-color" content="#070B0A">
    <title><?= e($pageTitle) ?> — Admin</title>
    <link rel="icon" href="/assets/favicon.svg">
    <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
    <script nonce="<?= e(csp_nonce()) ?>">
        (function () {
            var stored = localStorage.getItem('theme');
            var theme = stored || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.classList.toggle('light', theme === 'light');
            document.documentElement.classList.toggle('dark', theme !== 'light');
        })();
    </script>
</head>
<body class="min-h-screen bg-bg text-ink antialiased" x-data="{ navOpen: false }">
    <div class="admin-shell">
        <aside class="admin-side" :class="navOpen && 'is-open'">
            <a href="<?= e(url('/admin')) ?>" class="font-display text-xl font-semibold text-accent">EA Admin</a>
            <nav class="mt-6 flex flex-col gap-1" aria-label="Admin">
                <?php foreach ($nav as [$href, $label, $icon]): ?>
                    <a class="admin-link<?= ($path === rtrim($href, '/') || ($href !== '/admin' && str_starts_with($path, rtrim($href, '/')))) ? ' is-active' : '' ?>" href="<?= e(url($href)) ?>"><?= icon($icon, 'h-4 w-4') ?><span><?= e($label) ?></span></a>
                <?php endforeach; ?>
            </nav>
            <form method="post" action="<?= e(url('/admin/logout')) ?>" class="mt-6">
                <?= csrf_field() ?>
                <button class="admin-link w-full" type="submit"><?= icon('log-out', 'h-4 w-4') ?><span>Sign out</span></button>
            </form>
        </aside>
        <div class="admin-main">
            <header class="mb-6 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button type="button" class="icon-btn lg:hidden" @click="navOpen = !navOpen" aria-label="Open admin menu"><?= icon('menu', 'h-5 w-5') ?></button>
                    <h1 class="font-display text-2xl font-semibold"><?= e($pageTitle) ?></h1>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="icon-btn" data-theme-toggle aria-label="Toggle color theme"><?= icon('sun', 'h-5 w-5') ?></button>
                    <a class="btn-ghost" href="<?= e(url('/')) ?>" target="_blank" rel="noopener noreferrer">View site</a>
                </div>
            </header>
            <?php if (!empty($success)): ?><p class="toast-inline" data-toast="success"><?= e($success) ?></p><?php endif; ?>
            <?php if (!empty($error)): ?><p class="toast-inline is-error" data-toast="error"><?= e($error) ?></p><?php endif; ?>
            <?= $content ?>
        </div>
    </div>
    <div id="toasts" class="toasts" aria-live="polite"></div>
    <script src="<?= e(asset('/assets/js/alpine.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/admin.js')) ?>" defer></script>
</body>
</html>
