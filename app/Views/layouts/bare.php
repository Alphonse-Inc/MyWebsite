<?php
/** @var string $content */
/** @var string $pageTitle */
$pageTitle = $pageTitle ?? 'Portfolio';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#070B0A">
    <title><?= e($pageTitle) ?></title>
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
<body class="min-h-screen bg-bg text-ink antialiased">
    <div class="atmosphere" aria-hidden="true"></div>
    <main id="content" class="relative z-10"><?= $content ?></main>
    <script src="<?= e(asset('/assets/js/alpine.min.js')) ?>" defer></script>
    <script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
</body>
</html>
