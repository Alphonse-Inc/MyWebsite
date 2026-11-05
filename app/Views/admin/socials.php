<form class="glass mb-6 grid gap-3 p-5 md:grid-cols-2" method="post" action="<?= e(url('/admin/socials')) ?>">
    <?= csrf_field() ?>
    <input class="field-input" name="platform" placeholder="Platform key (linkedin, x, github)" required>
    <input class="field-input" name="label" placeholder="Label" required>
    <input class="field-input md:col-span-2" name="url" type="url" placeholder="https://" required>
    <input class="field-input" name="sort_order" type="number" value="0">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible" checked> Visible</label>
    <button class="btn-primary md:col-span-2" type="submit">Add link</button>
</form>
<?php foreach ($rows as $row): ?>
    <form class="glass mb-4 grid gap-3 p-5 md:grid-cols-2" method="post" action="<?= e(url('/admin/socials')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) $row['id']) ?>">
        <input class="field-input" name="platform" value="<?= e($row['platform']) ?>" required>
        <input class="field-input" name="label" value="<?= e($row['label']) ?>" required>
        <input class="field-input md:col-span-2" name="url" type="url" value="<?= e($row['url']) ?>" required>
        <input class="field-input" name="sort_order" type="number" value="<?= e((string) $row['sort_order']) ?>">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible"<?= (int) $row['is_visible'] === 1 ? ' checked' : '' ?>> Visible</label>
        <button class="btn-primary" type="submit">Save</button>
    </form>
    <form class="mb-8" method="post" action="<?= e(url('/admin/socials/' . $row['id'] . '/delete')) ?>" data-confirm="Delete this link?"><?= csrf_field() ?><button class="text-sm text-muted" type="submit">Delete</button></form>
<?php endforeach; ?>
