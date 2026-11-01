<div class="grid gap-4 md:grid-cols-4">
    <article class="glass p-5"><p class="text-sm text-muted">Published projects</p><p class="mt-2 font-display text-3xl font-semibold text-accent"><?= e((string) $projects) ?></p></article>
    <article class="glass p-5"><p class="text-sm text-muted">Unread messages</p><p class="mt-2 font-display text-3xl font-semibold text-accent"><?= e((string) ($messages['unread'] ?? 0)) ?></p></article>
    <article class="glass p-5"><p class="text-sm text-muted">Services</p><p class="mt-2 font-display text-3xl font-semibold text-accent"><?= e((string) $services) ?></p></article>
    <article class="glass p-5"><p class="text-sm text-muted">CV downloads</p><p class="mt-2 font-display text-3xl font-semibold text-accent"><?= e((string) $downloads) ?></p></article>
</div>
<div class="mt-6 flex flex-wrap gap-3">
    <a class="btn-primary" href="<?= e(url('/admin/projects/new')) ?>">Add project</a>
    <a class="btn-ghost" href="<?= e(url('/admin/messages')) ?>">Inbox</a>
    <a class="btn-ghost" href="<?= e(url('/admin/profile')) ?>">Edit profile</a>
</div>
<section class="mt-8">
    <h2 class="font-display text-xl font-semibold">Latest messages</h2>
    <div class="mt-4 overflow-x-auto">
        <table class="admin-table">
            <thead><tr><th>From</th><th>Subject</th><th>Status</th><th>When</th></tr></thead>
            <tbody>
            <?php if ($latest === []): ?>
                <tr><td colspan="4">No messages yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($latest as $row): ?>
                <tr>
                    <td><a class="text-accent" href="<?= e(url('/admin/messages/' . $row['id'])) ?>"><?= e($row['name']) ?></a></td>
                    <td><?= e($row['subject']) ?></td>
                    <td><?= e($row['status']) ?></td>
                    <td class="font-mono text-xs"><?= e($row['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
