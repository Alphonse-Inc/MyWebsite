<?php /** @var array<string, string> $settings */ ?>
<footer class="border-t border-line no-print">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 md:flex-row md:items-center md:justify-between md:px-6">
        <div>
            <p class="font-display text-lg font-semibold"><?= e($display) ?></p>
            <p class="mt-1 text-sm text-muted">© <?= e(date('Y')) ?> <?= e(setting($settings, 'footer_text', $display)) ?></p>
        </div>
        <ul class="flex items-center gap-3">
            <?php foreach ($socials as $social): ?>
                <li>
                    <a class="icon-btn" href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>">
                        <?= icon($social['platform'] === 'x' ? 'x-social' : (string) $social['platform'], 'h-5 w-5') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</footer>
