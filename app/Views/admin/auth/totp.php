<section class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <form class="glass w-full p-8" method="post" action="<?= e(url('/admin/login/totp')) ?>">
        <h1 class="font-display text-2xl font-semibold">Authenticator code</h1>
        <?php if (!empty($error)): ?><p class="mt-4 text-sm text-accent"><?= e($error) ?></p><?php endif; ?>
        <?= csrf_field() ?>
        <label class="field-label mt-6" for="code">6-digit code</label>
        <input class="field-input font-mono" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" required>
        <button class="btn-primary mt-6 w-full" type="submit">Continue</button>
    </form>
</section>
