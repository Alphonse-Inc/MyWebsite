<form class="glass mb-8 grid gap-3 p-5 md:grid-cols-2" method="post" action="<?= e(url('/admin/services')) ?>">
    <?= csrf_field() ?>
    <input class="field-input" name="name" placeholder="Name" required>
    <input class="field-input" name="icon" placeholder="Icon name, e.g. code" value="code">
    <textarea class="field-input md:col-span-2" name="description" placeholder="Description" required></textarea>
    <input class="field-input" name="sort_order" type="number" value="0">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible" value="1" checked> Visible</label>
    <button class="btn-primary md:col-span-2" type="submit">Add service</button>
</form>
<div class="space-y-4" data-reorder="/admin/services/reorder">
<?php foreach ($rows as $row): ?>
    <form class="glass grid gap-3 p-5 md:grid-cols-2" method="post" action="<?= e(url('/admin/services')) ?>" draggable="true" data-id="<?= e((string) $row['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) $row['id']) ?>">
        <input class="field-input" name="name" value="<?= e($row['name']) ?>" required>
        <input class="field-input" name="icon" value="<?= e($row['icon']) ?>">
        <textarea class="field-input md:col-span-2" name="description" required><?= e($row['description']) ?></textarea>
        <input class="field-input" name="sort_order" type="number" value="<?= e((string) $row['sort_order']) ?>">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible" value="1"<?= (int) $row['is_visible'] === 1 ? ' checked' : '' ?>> Visible</label>
        <div class="flex gap-3 md:col-span-2">
            <button class="btn-primary" type="submit">Save</button>
        </div>
    </form>
    <form method="post" action="<?= e(url('/admin/services/' . $row['id'] . '/delete')) ?>" data-confirm="Delete this service?"><?= csrf_field() ?><button class="text-sm text-muted" type="submit">Delete</button></form>
<?php endforeach; ?>
</div>
