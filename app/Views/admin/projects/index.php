<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex flex-wrap gap-2" method="get" action="<?= e(url('/admin/projects')) ?>">
        <input class="field-input w-auto" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search" aria-label="Search projects">
        <select class="field-input w-auto" name="category" aria-label="Category">
            <option value="">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e((string) $category['id']) ?>"<?= $filters['category'] === (string) $category['id'] ? ' selected' : '' ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="field-input w-auto" name="status" aria-label="Status">
            <option value="">All statuses</option>
            <option value="published"<?= $filters['status'] === 'published' ? ' selected' : '' ?>>Published</option>
            <option value="draft"<?= $filters['status'] === 'draft' ? ' selected' : '' ?>>Draft</option>
        </select>
        <button class="btn-ghost" type="submit">Filter</button>
    </form>
    <a class="btn-primary" href="<?= e(url('/admin/projects/new')) ?>">Add project</a>
</div>
<form method="post" action="<?= e(url('/admin/projects/bulk-delete')) ?>" data-confirm="Delete the selected projects and their images?">
    <?= csrf_field() ?>
    <div class="mb-3 flex gap-2">
        <button class="btn-ghost" type="submit">Delete selected</button>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table" data-reorder="/admin/projects/reorder">
            <thead>
                <tr>
                    <th></th><th></th><th>Project</th><th>Category</th><th>Status</th><th>Featured</th><th>Link</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project): ?>
                <tr draggable="true" data-id="<?= e((string) $project['id']) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= e((string) $project['id']) ?>" aria-label="Select <?= e($project['name']) ?>"></td>
                    <td class="cursor-grab text-muted" aria-hidden="true"><?= icon('grip-vertical', 'h-4 w-4') ?></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <?php if (!empty($project['cover'])): ?>
                                <img class="h-12 w-16 rounded-lg object-cover" src="<?= e(\App\Services\Media::baseUrl((int) $project['id'], (string) $project['cover']['stem']) . '-thumb.webp') ?>" alt="">
                            <?php endif; ?>
                            <a class="font-semibold text-accent" href="<?= e(url('/admin/projects/' . $project['id'] . '/edit')) ?>"><?= e($project['name']) ?></a>
                        </div>
                    </td>
                    <td><?= e((string) ($project['category_name'] ?? '')) ?></td>
                    <td>
                        <form method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/toggle')) ?>">
                            <?= csrf_field() ?>
                            <button class="tag" type="submit"><?= e($project['status']) ?></button>
                        </form>
                    </td>
                    <td><?= (int) $project['featured'] === 1 ? 'Yes' : 'No' ?></td>
                    <td><a class="text-accent" href="<?= e($project['url']) ?>" target="_blank" rel="noopener noreferrer">Open</a></td>
                    <td class="space-x-2 whitespace-nowrap">
                        <a class="text-accent" href="<?= e(url('/admin/projects/' . $project['id'] . '/edit')) ?>">Edit</a>
                        <form class="inline" method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/duplicate')) ?>"><?= csrf_field() ?><button class="text-accent" type="submit">Duplicate</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
