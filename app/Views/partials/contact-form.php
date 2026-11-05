<?php
/** @var array<string, string> $settings */
/** @var array<string, string> $form_errors */
/** @var array<string, string> $form_old */
$form_errors = $form_errors ?? [];
$form_old = $form_old ?? [];
$compact = $compact ?? false;
?>
<form class="contact-form space-y-4" method="post" action="<?= e(url('/contact')) ?>" data-contact-form novalidate>
    <?= csrf_field() ?>
    <div class="hp-field" aria-hidden="true">
        <label>Company<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
    </div>
    <div class="<?= $compact ? '' : 'grid gap-4 md:grid-cols-2' ?>">
        <div>
            <label class="field-label" for="contact-name"><?= e(setting($settings, 'form_name', 'Name')) ?></label>
            <input class="field-input" id="contact-name" name="name" required maxlength="120" autocomplete="name" value="<?= e(old_value($form_old, 'name')) ?>">
            <?php if (!empty($form_errors['name'])): ?><p class="field-error"><?= e($form_errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="<?= $compact ? 'mt-4' : '' ?>">
            <label class="field-label" for="contact-email"><?= e(setting($settings, 'form_email', 'Email')) ?></label>
            <input class="field-input" id="contact-email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e(old_value($form_old, 'email')) ?>">
            <?php if (!empty($form_errors['email'])): ?><p class="field-error"><?= e($form_errors['email']) ?></p><?php endif; ?>
        </div>
    </div>
    <div>
        <label class="field-label" for="contact-subject"><?= e(setting($settings, 'form_subject', 'Subject')) ?></label>
        <input class="field-input" id="contact-subject" name="subject" required maxlength="190" value="<?= e(old_value($form_old, 'subject')) ?>">
        <?php if (!empty($form_errors['subject'])): ?><p class="field-error"><?= e($form_errors['subject']) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="field-label" for="contact-message"><?= e(setting($settings, 'form_message', 'Message')) ?></label>
        <textarea class="field-input min-h-36" id="contact-message" name="message" required maxlength="5000"><?= e(old_value($form_old, 'message')) ?></textarea>
        <?php if (!empty($form_errors['message'])): ?><p class="field-error"><?= e($form_errors['message']) ?></p><?php endif; ?>
    </div>
    <button class="btn-primary" type="submit" data-magnetic>
        <span data-label><?= e(setting($settings, 'btn_send', 'Send Message')) ?></span>
        <?= icon('send', 'h-4 w-4') ?>
    </button>
    <p class="form-status text-sm text-muted" role="status" data-form-status><?= e($form_success ?? '') ?><?= e($form_error ?? '') ?></p>
</form>
