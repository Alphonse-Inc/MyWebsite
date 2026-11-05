<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex flex-wrap gap-2" method="get">
        <input class="field-input w-auto" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search" aria-label="Search messages">
        <select class="field-input w-auto" name="status" aria-label="Status">
            <option value="">All</option>
            <?php foreach (['unread', 'read', 'replied', 'spam'] as $status): ?>
                <option value="<?= e($status) ?>"<?= $filters['status'] === $status ? ' selected' : '' ?>><?= e($status) ?> (<?= e((string) ($counts[$status] ?? 0)) ?>)</option>
            <?php endforeach; ?>
        </select>
        <button class="btn-ghost" type="submit">Filter</button>
    </form>
    <a class="btn-ghost" href="<?= e(url('/admin/messages/export')) ?>">Export CSV</a>
</div>
<div class="overflow-x-auto">
    <table class="admin-table">
        <thead><tr><th>From</th><th>Subject</th><th>Status</th><th>When</th></tr></thead>
        <tbody>
        <?php if ($messages === []): ?><tr><td colspan="4">No messages.</td></tr><?php endif; ?>
        <?php foreach ($messages as $row): ?>
            <tr>
                <td><a class="text-accent" href="<?= e(url('/admin/messages/' . $row['id'])) ?>"><?= e($row['name']) ?></a><div class="text-xs text-muted"><?= e($row['email']) ?></div></td>
                <td><?= e($row['subject']) ?></td>
                <td><?= e($row['status']) ?></td>
                <td class="font-mono text-xs"><?= e($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
