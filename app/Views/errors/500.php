<section class="section">
    <div class="pub-wrap" style="max-width:36rem">
        <p class="eyebrow">500</p>
        <h1 class="section-title mt-4"><?= e(setting($settings ?? [], 'error_500_title', 'Something went wrong')) ?></h1>
        <p class="pub-lede"><?= e(setting($settings ?? [], 'error_500_body', 'The page could not be loaded. Please try again shortly.')) ?></p>
        <a class="btn-primary mt-10" href="<?= e(url('/')) ?>">Back home</a>
    </div>
</section>
