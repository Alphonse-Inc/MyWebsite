<?php if (!empty($mustChange)): ?><p class="mb-4 max-w-xl rounded-2xl border border-accent/40 bg-accent/10 p-4 text-sm">Change this password before using the rest of the admin.</p><?php endif; ?>
<form class="grid max-w-md gap-4" method="post" action="<?= e(url('/admin/password')) ?>">
    <?= csrf_field() ?>
    <label class="field-label" for="current_password">Current password</label>
    <input class="field-input" id="current_password" name="current_password" type="password" required autocomplete="current-password">
    <?php if (!empty($errors['current_password'])): ?><p class="field-error"><?= e($errors['current_password']) ?></p><?php endif; ?>
    <label class="field-label" for="password">New password</label>
    <input class="field-input" id="password" name="password" type="password" required minlength="12" autocomplete="new-password">
    <?php if (!empty($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
    <label class="field-label" for="password_confirmation">Repeat new password</label>
    <input class="field-input" id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password">
    <button class="btn-primary" type="submit">Update password</button>
</form>
