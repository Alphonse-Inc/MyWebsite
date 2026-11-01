<form class="glass mb-8 grid gap-3 p-5" method="post" action="<?= e(url('/admin/education')) ?>">
    <?= csrf_field() ?>
    <div class="grid gap-3 md:grid-cols-2">
        <input class="field-input" name="title" placeholder="Title" required>
        <input class="field-input" name="institution" placeholder="Institution" required>
        <input class="field-input" name="location" placeholder="Location">
        <input class="field-input" name="start_label" placeholder="Start">
        <input class="field-input" name="end_label" placeholder="End">
        <input class="field-input" name="sort_order" type="number" value="0">
    </div>
    <textarea class="field-input" name="description" placeholder="Description"></textarea>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible" checked> Visible</label>
    <button class="btn-primary" type="submit">Add education</button>
</form>
<?php foreach ($rows as $row): ?>
    <form class="glass mb-4 grid gap-3 p-5" method="post" action="<?= e(url('/admin/education')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) $row['id']) ?>">
        <div class="grid gap-3 md:grid-cols-2">
            <input class="field-input" name="title" value="<?= e($row['title']) ?>" required>
            <input class="field-input" name="institution" value="<?= e($row['institution']) ?>" required>
            <input class="field-input" name="location" value="<?= e($row['location']) ?>">
            <input class="field-input" name="start_label" value="<?= e($row['start_label']) ?>">
            <input class="field-input" name="end_label" value="<?= e($row['end_label']) ?>">
            <input class="field-input" name="sort_order" type="number" value="<?= e((string) $row['sort_order']) ?>">
        </div>
        <textarea class="field-input" name="description"><?= e($row['description']) ?></textarea>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_visible"<?= (int) $row['is_visible'] === 1 ? ' checked' : '' ?>> Visible</label>
        <button class="btn-primary" type="submit">Save</button>
    </form>
    <form class="mb-8" method="post" action="<?= e(url('/admin/education/' . $row['id'] . '/delete')) ?>" data-confirm="Delete this entry?"><?= csrf_field() ?><button class="text-sm text-muted" type="submit">Delete</button></form>
<?php endforeach; ?>
