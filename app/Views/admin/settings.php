<form class="grid max-w-3xl gap-4" method="post" action="<?= e(url('/admin/settings')) ?>">
    <?= csrf_field() ?>
    <?php
    $lines = [
        'site_title' => 'Site title', 'meta_description' => 'Default meta description', 'meta_keywords' => 'Keywords',
        'meta_author' => 'Author', 'footer_text' => 'Footer name', 'analytics_id' => 'Analytics ID',
        'career_start_year' => 'Career start year', 'incident_remedy_percent' => 'Incident remedy percent',
        'smtp_host' => 'SMTP host', 'smtp_port' => 'SMTP port', 'smtp_user' => 'SMTP user',
        'smtp_from_email' => 'From email', 'smtp_from_name' => 'From name', 'notify_email' => 'Notify email',
        'auto_reply_subject' => 'Auto-reply subject',
    ];
    foreach ($lines as $key => $label): ?>
        <label class="field-label" for="<?= e($key) ?>"><?= e($label) ?></label>
        <input class="field-input" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
    <?php endforeach; ?>
    <label class="field-label" for="smtp_pass">SMTP password</label>
    <input class="field-input" id="smtp_pass" name="smtp_pass" type="password" placeholder="Leave blank to keep the current password" autocomplete="new-password">
    <label class="field-label" for="smtp_encryption">SMTP encryption</label>
    <select class="field-input" id="smtp_encryption" name="smtp_encryption">
        <?php foreach (['tls', 'ssl', 'none'] as $mode): ?>
            <option value="<?= e($mode) ?>"<?= ($settings['smtp_encryption'] ?? '') === $mode ? ' selected' : '' ?>><?= e($mode) ?></option>
        <?php endforeach; ?>
    </select>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="auto_reply_enabled"<?= ($settings['auto_reply_enabled'] ?? '0') === '1' ? ' checked' : '' ?>> Send an automatic reply</label>
    <label class="field-label" for="auto_reply_body">Auto-reply body</label>
    <textarea class="field-input" id="auto_reply_body" name="auto_reply_body"><?= e($settings['auto_reply_body'] ?? '') ?></textarea>
    <h2 class="mt-4 font-display text-xl font-semibold">Interface labels</h2>
    <?php foreach (['label_about','label_services','label_resume','label_experience','label_education','label_skills','label_projects','label_contact','label_quote','label_get_in_touch','btn_download_cv','btn_contact','btn_send','btn_visit','btn_view_project','form_name','form_email','form_subject','form_message','contact_success','contact_error','error_404_title','error_404_body','error_500_title','error_500_body'] as $key): ?>
        <label class="field-label" for="<?= e($key) ?>"><?= e($key) ?></label>
        <input class="field-input" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
    <?php endforeach; ?>
    <button class="btn-primary" type="submit">Save settings</button>
</form>
