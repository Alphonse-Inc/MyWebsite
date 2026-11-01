<section class="section">
    <div class="mx-auto max-w-xl px-4 py-20 text-center md:px-6">
        <p class="eyebrow">404</p>
        <h1 class="section-title"><?= e($cvMissing ?? false ? 'CV unavailable' : setting($settings ?? [], 'error_404_title', 'Page not found')) ?></h1>
        <p class="mt-4 text-muted"><?= e($cvMissing ?? false ? 'A CV file has not been uploaded yet.' : setting($settings ?? [], 'error_404_body', 'That page is not on this site.')) ?></p>
        <a class="btn-primary mt-8" href="<?= e(url('/')) ?>">Back home</a>
    </div>
</section>
