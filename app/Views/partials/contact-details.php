<?php /** @var array<string, string> $settings */ ?>
<ul class="mt-8 space-y-4">
    <li class="flex items-start gap-3">
        <span class="icon-badge"><?= icon('map-pin', 'h-5 w-5') ?></span>
        <span><span class="block font-mono text-xs uppercase tracking-wider text-muted"><?= e(setting($settings, 'label_address', 'Address')) ?></span><?= e(setting($settings, 'contact_location')) ?></span>
    </li>
    <li class="flex items-start gap-3">
        <span class="icon-badge"><?= icon('mail', 'h-5 w-5') ?></span>
        <span><span class="block font-mono text-xs uppercase tracking-wider text-muted"><?= e(setting($settings, 'label_email', 'Email')) ?></span><a class="text-accent" href="mailto:<?= e(setting($settings, 'contact_email')) ?>"><?= e(setting($settings, 'contact_email')) ?></a></span>
    </li>
    <li class="flex items-start gap-3">
        <span class="icon-badge"><?= icon('phone', 'h-5 w-5') ?></span>
        <span><span class="block font-mono text-xs uppercase tracking-wider text-muted"><?= e(setting($settings, 'label_phone', 'Phone')) ?></span><a class="text-accent" href="tel:+<?= e(setting($settings, 'contact_phone_e164')) ?>"><?= e(setting($settings, 'contact_phone')) ?></a></span>
    </li>
    <li class="flex items-start gap-3">
        <span class="icon-badge"><?= icon('message-circle', 'h-5 w-5') ?></span>
        <span><span class="block font-mono text-xs uppercase tracking-wider text-muted"><?= e(setting($settings, 'label_whatsapp', 'WhatsApp')) ?></span><a class="text-accent" href="<?= e(setting($settings, 'contact_whatsapp')) ?>" target="_blank" rel="noopener noreferrer"><?= e(setting($settings, 'contact_phone')) ?></a></span>
    </li>
    <li class="flex items-start gap-3">
        <span class="icon-badge"><?= icon('circle-user', 'h-5 w-5') ?></span>
        <span><span class="block font-mono text-xs uppercase tracking-wider text-muted"><?= e(setting($settings, 'label_freelance', 'Freelance')) ?></span><?= e(setting($settings, 'freelance_status')) ?></span>
    </li>
</ul>
<?php $map = setting($settings, 'map_embed'); ?>
<?php if ($map !== ''): ?>
    <div class="mt-6 overflow-hidden rounded-2xl border border-line">
        <iframe title="Map of <?= e(setting($settings, 'contact_location')) ?>" src="<?= e($map) ?>" class="h-64 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
<?php endif; ?>
