<form class="glass mb-6 grid gap-3 p-5 md:grid-cols-4" method="post" action="<?= e(url('/admin/skills/groups')) ?>">
    <?= csrf_field() ?>
    <input class="field-input" name="name" placeholder="Group name" required>
    <select class="field-input" name="display_type"><option value="bar">Bars</option><option value="ring">Rings</option><option value="tag">Tags</option></select>
    <input class="field-input" name="sort_order" type="number" value="0">
    <button class="btn-primary" type="submit">Add group</button>
</form>
<?php foreach ($groups as $group): ?>
    <section class="glass mb-6 p-5">
        <form class="grid gap-3 md:grid-cols-4" method="post" action="<?= e(url('/admin/skills/groups')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= e((string) $group['id']) ?>">
            <input class="field-input" name="name" value="<?= e($group['name']) ?>" required>
            <select class="field-input" name="display_type">
                <?php foreach (['bar' => 'Bars', 'ring' => 'Rings', 'tag' => 'Tags'] as $type => $label): ?>
                    <option value="<?= e($type) ?>"<?= $group['display_type'] === $type ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <input class="field-input" name="sort_order" type="number" value="<?= e((string) $group['sort_order']) ?>">
            <button class="btn-ghost" type="submit">Save group</button>
        </form>
        <form class="mt-2" method="post" action="<?= e(url('/admin/skills/groups/' . $group['id'] . '/delete')) ?>" data-confirm="Delete this group and its skills?"><?= csrf_field() ?><button class="text-sm text-muted" type="submit">Delete group</button></form>
        <ul class="mt-4 space-y-3">
            <?php foreach ($group['skills'] as $skill): ?>
                <li>
                    <form class="grid gap-2 md:grid-cols-5" method="post" action="<?= e(url('/admin/skills')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e((string) $skill['id']) ?>">
                        <input type="hidden" name="group_id" value="<?= e((string) $group['id']) ?>">
                        <input class="field-input md:col-span-2" name="name" value="<?= e($skill['name']) ?>" required>
                        <input class="field-input" name="level" placeholder="Level or blank" value="<?= e($skill['level'] === null ? '' : (string) $skill['level']) ?>">
                        <input class="field-input" name="sort_order" type="number" value="<?= e((string) $skill['sort_order']) ?>">
                        <button class="btn-ghost" type="submit">Save</button>
                    </form>
                    <form method="post" action="<?= e(url('/admin/skills/' . $skill['id'] . '/delete')) ?>"><?= csrf_field() ?><button class="text-xs text-muted" type="submit">Remove skill</button></form>
                </li>
            <?php endforeach; ?>
        </ul>
        <form class="mt-4 grid gap-2 md:grid-cols-4" method="post" action="<?= e(url('/admin/skills')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="group_id" value="<?= e((string) $group['id']) ?>">
            <input class="field-input" name="name" placeholder="New skill" required>
            <input class="field-input" name="level" placeholder="Level">
            <input class="field-input" name="sort_order" type="number" value="0">
            <button class="btn-primary" type="submit">Add skill</button>
        </form>
    </section>
<?php endforeach; ?>
