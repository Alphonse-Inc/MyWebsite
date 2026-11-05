<?php
/** @var array<string, string> $settings */
$about = setting($settings, 'label_about', 'About');
$resume = setting($settings, 'label_resume', 'Resume');
$projects = setting($settings, 'label_projects', 'Projects');
$contact = setting($settings, 'label_contact', 'Contact');
$skills = setting($settings, 'label_skills', 'My Skills');
?>
<header class="pub-header sticky top-0 z-50 no-print" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="pub-header-bg" aria-hidden="true"></div>
    <div class="pub-wrap">
        <a href="<?= e(url('/')) ?>" class="pub-mark" aria-label="<?= e($display) ?>">
            <span>EA</span>
        </a>
        <nav class="pub-nav hidden md:flex" aria-label="Primary">
            <a class="nav-link<?= e(selected_nav($active, 'home')) ?>" href="<?= e(url('/#about')) ?>"><?= e($about) ?></a>
            <a class="nav-link<?= e(selected_nav($active, 'resume')) ?>" href="<?= e(url('/resume')) ?>"><?= e($resume) ?></a>
            <a class="nav-link" href="<?= e(url('/#skills')) ?>"><?= e($skills) ?></a>
            <a class="nav-link<?= e(selected_nav($active, 'projects')) ?>" href="<?= e(url('/projects')) ?>"><?= e($projects) ?></a>
            <a class="nav-link<?= e(selected_nav($active, 'contact')) ?>" href="<?= e(url('/contact')) ?>"><?= e($contact) ?></a>
        </nav>
        <div class="flex items-center gap-2">
            <button type="button" class="theme-toggle icon-btn" data-theme-toggle aria-label="Toggle color theme">
                <span class="theme-icon-dark"><?= icon('moon', 'h-5 w-5') ?></span>
                <span class="theme-icon-light"><?= icon('sun', 'h-5 w-5') ?></span>
            </button>
            <a class="btn-ghost hidden sm:inline-flex" data-magnetic href="<?= e(url('/contact')) ?>"><?= e(setting($settings, 'btn_contact', 'Contact Me')) ?></a>
            <button type="button" class="icon-btn md:hidden" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu" aria-label="Open menu">
                <?= icon('menu', 'h-5 w-5') ?>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="mobile-menu md:hidden" x-show="open" x-cloak x-transition.opacity @click.outside="open = false">
        <div class="pub-wrap flex items-center justify-between py-4">
            <span class="pub-mark"><span>EA</span></span>
            <button type="button" class="icon-btn" @click="open = false" aria-label="Close menu"><?= icon('x', 'h-5 w-5') ?></button>
        </div>
        <nav class="pub-wrap flex flex-col" aria-label="Mobile">
            <a class="mobile-link" href="<?= e(url('/#about')) ?>" @click="open = false"><?= e($about) ?></a>
            <a class="mobile-link" href="<?= e(url('/resume')) ?>"><?= e($resume) ?></a>
            <a class="mobile-link" href="<?= e(url('/#skills')) ?>" @click="open = false"><?= e($skills) ?></a>
            <a class="mobile-link" href="<?= e(url('/projects')) ?>"><?= e($projects) ?></a>
            <a class="mobile-link" href="<?= e(url('/contact')) ?>"><?= e($contact) ?></a>
            <a class="btn-primary mt-8" href="<?= e(url('/cv')) ?>"><?= e(setting($settings, 'btn_download_cv', 'Download CV')) ?></a>
        </nav>
    </div>
</header>
