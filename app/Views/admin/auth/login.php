<section class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <form class="glass w-full p-8" method="post" action="<?= e(url('/admin/login')) ?>">
        <p class="font-display text-2xl font-semibold text-accent">EA</p>
        <h1 class="mt-2 font-display text-2xl font-semibold">Admin sign in</h1>
        <?php if (!empty($error)): ?><p class="mt-4 text-sm text-accent"><?= e($error) ?></p><?php endif; ?>
        <?= csrf_field() ?>
        <label class="field-label mt-6" for="email">Email</label>
        <input class="field-input" id="email" name="email" type="email" required autocomplete="username" value="<?= e(old_value($old ?? [], 'email')) ?>">
        <label class="field-label mt-4" for="password">Password</label>
        <input class="field-input" id="password" name="password" type="password" required autocomplete="current-password">
        <button class="btn-primary mt-6 w-full" type="submit">Sign in</button>
        <p class="mt-4 text-sm"><a class="text-accent" href="<?= e(url('/admin/forgot-password')) ?>">Forgot password</a></p>
    </form>
</section>
