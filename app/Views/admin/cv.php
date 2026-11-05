<p class="text-sm text-muted">Downloads so far: <?= e($downloads) ?></p>
<?php if ($path !== ''): ?><p class="mt-2">Current file is ready at <a class="text-accent" href="<?= e(url('/cv')) ?>">/cv</a>.</p><?php else: ?><p class="mt-2">No PDF uploaded yet.</p><?php endif; ?>
<form class="mt-6 max-w-lg space-y-4" method="post" action="<?= e(url('/admin/cv')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label class="field-label" for="cv">PDF file</label>
    <input class="field-input" id="cv" name="cv" type="file" accept="application/pdf" required>
    <button class="btn-primary" type="submit">Upload CV</button>
</form>
<?php if ($path !== ''): ?>
<form class="mt-4" method="post" action="<?= e(url('/admin/cv/delete')) ?>" data-confirm="Remove the CV file?"><?= csrf_field() ?><button class="btn-ghost" type="submit">Remove CV</button></form>
<?php endif; ?>
