<section class="section">
    <div class="pub-wrap pub-contact">
        <div>
            <div class="pub-section-head">
                <p class="eyebrow"><?= e(setting($settings, 'label_contact', 'Contact')) ?></p>
                <h1 class="section-title"><?= e(setting($settings, 'label_get_in_touch', 'Get in Touch')) ?></h1>
            </div>
            <?php partial('partials/contact-details', compact('settings', 'profile')); ?>
        </div>
        <div class="glass pub-panel">
            <?php $compact = false; partial('partials/contact-form', get_defined_vars()); ?>
        </div>
    </div>
</section>
