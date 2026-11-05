<?php /** @var array<string, string> $settings */ ?>
<footer class="pub-footer no-print">
    <div class="pub-wrap pub-footer-inner">
        <div>
            <p class="pub-footer-name"><?= e($display) ?></p>
            <p class="mt-2 text-sm text-muted">© <?= e(date('Y')) ?> <?= e(setting($settings, 'footer_text', $display)) ?></p>
        </div>
        <ul class="pub-socials mt-0">
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
