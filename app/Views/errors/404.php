<section class="section">
    <div class="pub-wrap" style="max-width:36rem">
        <p class="eyebrow">404</p>
        <h1 class="section-title mt-4"><?= e($cvMissing ?? false ? 'CV unavailable' : setting($settings ?? [], 'error_404_title', 'Page not found')) ?></h1>
        <p class="pub-lede"><?= e($cvMissing ?? false ? 'A CV file has not been uploaded yet.' : setting($settings ?? [], 'error_404_body', 'That page is not on this site.')) ?></p>
        <a class="btn-primary mt-10" href="<?= e(url('/')) ?>">Back home</a>
    </div>
</section>
