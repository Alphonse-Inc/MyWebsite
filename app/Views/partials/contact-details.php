<?php /** @var array<string, string> $settings */ ?>
<ul class="pub-facts">
    <li class="pub-fact">
        <span class="icon-badge"><?= icon('map-pin', 'h-5 w-5') ?></span>
        <span><small><?= e(setting($settings, 'label_address', 'Address')) ?></small><?= e(setting($settings, 'contact_location')) ?></span>
    </li>
    <li class="pub-fact">
        <span class="icon-badge"><?= icon('mail', 'h-5 w-5') ?></span>
        <span><small><?= e(setting($settings, 'label_email', 'Email')) ?></small><a class="text-accent" href="mailto:<?= e(setting($settings, 'contact_email')) ?>"><?= e(setting($settings, 'contact_email')) ?></a></span>
    </li>
    <li class="pub-fact">
        <span class="icon-badge"><?= icon('phone', 'h-5 w-5') ?></span>
        <span><small><?= e(setting($settings, 'label_phone', 'Phone')) ?></small><a class="text-accent" href="tel:+<?= e(setting($settings, 'contact_phone_e164')) ?>"><?= e(setting($settings, 'contact_phone')) ?></a></span>
    </li>
    <li class="pub-fact">
        <span class="icon-badge"><?= icon('message-circle', 'h-5 w-5') ?></span>
        <span><small><?= e(setting($settings, 'label_whatsapp', 'WhatsApp')) ?></small><a class="text-accent" href="<?= e(setting($settings, 'contact_whatsapp')) ?>" target="_blank" rel="noopener noreferrer"><?= e(setting($settings, 'contact_phone')) ?></a></span>
    </li>
    <li class="pub-fact">
        <span class="icon-badge"><?= icon('circle-user', 'h-5 w-5') ?></span>
        <span><small><?= e(setting($settings, 'label_freelance', 'Freelance')) ?></small><?= e(setting($settings, 'freelance_status')) ?></span>
    </li>
</ul>
<?php $map = setting($settings, 'map_embed'); ?>
<?php if ($map !== ''): ?>
    <div class="mt-8 overflow-hidden rounded-2xl border border-line">
        <iframe title="Map of <?= e(setting($settings, 'contact_location')) ?>" src="<?= e($map) ?>" class="h-64 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
<?php endif; ?>
