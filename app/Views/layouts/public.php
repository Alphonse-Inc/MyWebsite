<?php
/** @var string $content */
/** @var array<string, mixed> $profile */
/** @var array<string, string> $settings */
/** @var string $title */
/** @var string $description */
/** @var string $active */
$profile = $profile ?? [];
$settings = $settings ?? [];
$socials = $socials ?? [];
$title = $title ?? setting($settings, 'site_title', 'Emmanuel Oluwamuyiwa Afolayan');
$description = $description ?? setting($settings, 'meta_description', '');
$canonical = $canonical ?? url('/');
$active = $active ?? '';
$og = setting($settings, 'og_image');
if ($og !== '' && !str_starts_with($og, 'http')) {
    $og = url($og);
}
$display = (string) ($profile['display_name'] ?? 'Emmanuel Afolayan');
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta name="keywords" content="<?= e(setting($settings, 'meta_keywords')) ?>">
    <meta name="author" content="<?= e(setting($settings, 'meta_author', 'iam_alphonse')) ?>">
    <meta name="theme-color" content="#070B0A">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <link rel="icon" href="<?= e(setting($settings, 'favicon', '/assets/favicon.svg')) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if ($og !== ''): ?><meta property="og:image" content="<?= e($og) ?>"><?php endif; ?>
    <meta name="twitter:card" content="<?= $og !== '' ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($description) ?>">
    <?php if ($og !== ''): ?><meta name="twitter:image" content="<?= e($og) ?>"><?php endif; ?>
    <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/vendor/swiper/swiper-bundle.min.css')) ?>">
    <script nonce="<?= e(csp_nonce()) ?>">
        (function () {
            var stored = localStorage.getItem('theme');
            var theme = stored || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.classList.toggle('light', theme === 'light');
            document.documentElement.classList.toggle('dark', theme !== 'light');
            document.documentElement.style.colorScheme = theme;
            var meta = document.querySelector('meta[name="theme-color"]');
            if (meta) meta.setAttribute('content', theme === 'light' ? '#F6FAF8' : '#070B0A');
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!reduce) document.documentElement.classList.add('js');
        })();
    </script>
    <?php if (!empty($jsonLd)): ?>
        <script type="application/ld+json" nonce="<?= e(csp_nonce()) ?>"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <?php endif; ?>
    <?php $analytics = setting($settings, 'analytics_id'); ?>
    <?php if ($analytics !== '' && preg_match('/^(G|UA|GT)-[A-Z0-9-]+$/', $analytics) === 1): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($analytics) ?>"></script>
        <script nonce="<?= e(csp_nonce()) ?>">
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', <?= json_encode($analytics) ?>);
        </script>
    <?php endif; ?>
</head>
<body class="min-h-screen bg-bg text-ink antialiased">
    <a class="skip-link" href="#content">Skip to content</a>
    <div class="atmosphere no-print" aria-hidden="true"></div>
    <div class="noise no-print" aria-hidden="true"></div>
    <div id="preloader" class="preloader no-print" aria-hidden="true"><span>E</span><span>A</span></div>
    <div class="cursor-dot no-print" aria-hidden="true"></div>
    <div class="cursor-ring no-print" aria-hidden="true"></div>
    <?php partial('partials/header', compact('profile', 'settings', 'active', 'display')); ?>
    <main id="content">
        <?= $content ?>
    </main>
    <?php partial('partials/footer', compact('profile', 'settings', 'socials', 'display')); ?>
    <div id="toasts" class="toasts no-print" aria-live="polite"></div>
    <script src="<?= e(asset('/assets/js/gsap.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/ScrollTrigger.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/swiper-bundle.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/alpine.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
</body>
</html>
