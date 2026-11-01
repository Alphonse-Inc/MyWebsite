<section class="section">
    <div class="mx-auto max-w-xl px-4 py-20 text-center md:px-6">
        <p class="eyebrow">500</p>
        <h1 class="section-title"><?= e(setting($settings ?? [], 'error_500_title', 'Something went wrong')) ?></h1>
        <p class="mt-4 text-muted"><?= e(setting($settings ?? [], 'error_500_body', 'The page could not be loaded. Please try again shortly.')) ?></p>
        <a class="btn-primary mt-8" href="<?= e(url('/')) ?>">Back home</a>
    </div>
</section>
