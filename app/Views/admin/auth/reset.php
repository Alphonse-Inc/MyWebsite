<section class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <form class="glass w-full p-8" method="post" action="<?= e(url('/admin/reset-password/' . $token)) ?>">
        <h1 class="font-display text-2xl font-semibold">Choose a new password</h1>
        <?php if (!empty($error)): ?><p class="mt-4 text-sm text-accent"><?= e($error) ?></p><?php endif; ?>
        <?= csrf_field() ?>
        <label class="field-label mt-6" for="password">New password</label>
        <input class="field-input" id="password" name="password" type="password" required minlength="12" autocomplete="new-password">
        <label class="field-label mt-4" for="password_confirmation">Repeat password</label>
        <input class="field-input" id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password">
        <button class="btn-primary mt-6 w-full" type="submit">Update password</button>
    </form>
</section>
