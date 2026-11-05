<form class="grid max-w-2xl gap-4" method="post" action="<?= e(url('/admin/contact')) ?>">
    <?= csrf_field() ?>
    <?php
    $fields = [
        'contact_email' => 'Email',
        'contact_phone' => 'Phone',
        'contact_phone_e164' => 'Phone digits for tel and WhatsApp',
        'contact_location' => 'Location',
        'contact_whatsapp' => 'WhatsApp URL',
        'freelance_status' => 'Freelance status',
        'label_address' => 'Address label',
        'label_email' => 'Email label',
        'label_phone' => 'Phone label',
        'label_whatsapp' => 'WhatsApp label',
        'label_freelance' => 'Freelance label',
    ];
    foreach ($fields as $key => $label): ?>
        <label class="field-label" for="<?= e($key) ?>"><?= e($label) ?></label>
        <input class="field-input" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
    <?php endforeach; ?>
    <label class="field-label" for="map_embed">Map embed URL</label>
    <input class="field-input" id="map_embed" name="map_embed" value="<?= e($settings['map_embed'] ?? '') ?>">
    <button class="btn-primary" type="submit">Save contact details</button>
</form>
