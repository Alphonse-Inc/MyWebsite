<section class="section">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 md:px-6 lg:grid-cols-2">
        <div>
            <p class="eyebrow"><?= e(setting($settings, 'label_contact', 'Contact')) ?></p>
            <h1 class="section-title"><?= e(setting($settings, 'label_get_in_touch', 'Get in Touch')) ?></h1>
            <?php partial('partials/contact-details', compact('settings', 'profile')); ?>
        </div>
        <div class="glass p-6 md:p-8">
            <?php $compact = false; partial('partials/contact-form', get_defined_vars()); ?>
        </div>
    </div>
</section>
