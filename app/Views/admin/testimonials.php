<p class="mb-4 text-sm text-muted">This list starts empty. Nothing is shown on the public site until you mark an entry visible.</p>
<form class="glass mb-6 grid gap-3 p-5" method="post" action="<?= e(url('/admin/testimonials')) ?>">
    <?= csrf_field() ?>
    <input class="field-input" name="author_name" placeholder="Name" required>
    <input class="field-input" name="author_role" placeholder="Role">
    <textarea class="field-input" name="quote" placeholder="Quote" required></textarea>
    <input class="field-input" name="sort_order" type="number" value="0">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible"> Visible on the site</label>
    <button class="btn-primary" type="submit">Add testimonial</button>
</form>
<?php if ($rows === []): ?><p class="text-muted">No testimonials yet.</p><?php endif; ?>
<?php foreach ($rows as $row): ?>
    <form class="glass mb-4 grid gap-3 p-5" method="post" action="<?= e(url('/admin/testimonials')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) $row['id']) ?>">
        <input class="field-input" name="author_name" value="<?= e($row['author_name']) ?>" required>
        <input class="field-input" name="author_role" value="<?= e($row['author_role']) ?>">
        <textarea class="field-input" name="quote" required><?= e($row['quote']) ?></textarea>
        <input class="field-input" name="sort_order" type="number" value="<?= e((string) $row['sort_order']) ?>">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible"<?= (int) $row['is_visible'] === 1 ? ' checked' : '' ?>> Visible</label>
        <button class="btn-primary" type="submit">Save</button>
    </form>
    <form class="mb-8" method="post" action="<?= e(url('/admin/testimonials/' . $row['id'] . '/delete')) ?>" data-confirm="Delete this testimonial?"><?= csrf_field() ?><button class="text-sm text-muted" type="submit">Delete</button></form>
<?php endforeach; ?>
