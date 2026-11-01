<?php $m = $message; ?>
<article class="glass max-w-3xl p-6">
    <p class="font-mono text-xs text-muted"><?= e($m['created_at']) ?> · <?= e($m['status']) ?> · <?= e((string) $m['ip']) ?></p>
    <h2 class="mt-2 font-display text-2xl font-semibold"><?= e($m['subject']) ?></h2>
    <p class="mt-2"><?= e($m['name']) ?> · <a class="text-accent" href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></p>
    <div class="mt-4 whitespace-pre-wrap leading-relaxed"><?= e($m['body']) ?></div>
</article>
<div class="mt-4 flex flex-wrap gap-3">
    <?php foreach (['read', 'unread', 'spam'] as $status): ?>
        <form method="post" action="<?= e(url('/admin/messages/' . $m['id'] . '/status')) ?>"><?= csrf_field() ?><input type="hidden" name="status" value="<?= e($status) ?>"><button class="btn-ghost" type="submit">Mark <?= e($status) ?></button></form>
    <?php endforeach; ?>
    <form method="post" action="<?= e(url('/admin/messages/' . $m['id'] . '/delete')) ?>" data-confirm="Delete this message?"><?= csrf_field() ?><button class="btn-ghost" type="submit">Delete</button></form>
</div>
<form class="mt-8 max-w-3xl space-y-3" method="post" action="<?= e(url('/admin/messages/' . $m['id'] . '/reply')) ?>">
    <?= csrf_field() ?>
    <label class="field-label" for="reply">Reply by email</label>
    <textarea class="field-input min-h-36" id="reply" name="reply" required><?= e(old_value($old ?? [], 'reply')) ?></textarea>
    <?php if (!empty($errors['reply'])): ?><p class="field-error"><?= e($errors['reply']) ?></p><?php endif; ?>
    <button class="btn-primary" type="submit">Send reply</button>
</form>
