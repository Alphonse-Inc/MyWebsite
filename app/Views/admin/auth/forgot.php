<section class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <form class="glass w-full p-8" method="post" action="<?= e(url('/admin/forgot-password')) ?>">
        <h1 class="font-display text-2xl font-semibold">Reset password</h1>
        <?php if (!empty($error)): ?><p class="mt-4 text-sm text-accent"><?= e($error) ?></p><?php endif; ?>
        <?php if (!empty($success)): ?><p class="mt-4 text-sm text-accent"><?= e($success) ?></p><?php endif; ?>
        <?= csrf_field() ?>
        <label class="field-label mt-6" for="email">Email</label>
        <input class="field-input" id="email" name="email" type="email" required>
        <button class="btn-primary mt-6 w-full" type="submit">Send reset link</button>
        <p class="mt-4 text-sm"><a class="text-accent" href="<?= e(url('/admin/login')) ?>">Back to sign in</a></p>
    </form>
</section>
