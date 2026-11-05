<section class="max-w-xl space-y-6">
    <p class="text-sm text-muted">Optional authenticator codes. Leave this off unless you want a second step at sign-in.</p>
    <?php if ((int) ($user['totp_enabled'] ?? 0) === 1): ?>
        <p class="text-accent">Authenticator app is on.</p>
        <form class="grid gap-3" method="post" action="<?= e(url('/admin/security/totp/disable')) ?>">
            <?= csrf_field() ?>
            <label class="field-label" for="current_password">Current password</label>
            <input class="field-input" id="current_password" name="current_password" type="password" required>
            <button class="btn-ghost" type="submit">Turn off authenticator</button>
        </form>
    <?php else: ?>
        <form method="post" action="<?= e(url('/admin/security/totp')) ?>"><?= csrf_field() ?><button class="btn-primary" type="submit">Generate a secret</button></form>
        <?php if (!empty($pendingSecret)): ?>
            <div class="glass p-4">
                <p class="text-sm">Add this secret to your authenticator app, then confirm with a code.</p>
                <p class="mt-2 break-all font-mono text-accent"><?= e($pendingSecret) ?></p>
                <p class="mt-2 break-all text-xs text-muted"><?= e((string) $pendingUri) ?></p>
                <form class="mt-4 grid gap-3" method="post" action="<?= e(url('/admin/security/totp/confirm')) ?>">
                    <?= csrf_field() ?>
                    <label class="field-label" for="code">Code</label>
                    <input class="field-input" id="code" name="code" inputmode="numeric" required>
                    <button class="btn-primary" type="submit">Enable</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
