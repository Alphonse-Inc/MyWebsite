<?php
$editing = $project !== null;
$action = $editing ? url('/admin/projects/' . $project['id']) : url('/admin/projects');
$errors = $errors ?? [];
$old = $old ?? [];
$value = static function (string $key, $fallback = '') use ($project, $old) {
    if (array_key_exists($key, $old)) {
        return $old[$key];
    }
    if ($project === null) {
        return $fallback;
    }
    if ($key === 'technologies') {
        return implode(', ', $project['technologies'] ?? []);
    }
    if ($key === 'year') {
        return (string) ($project['year'] ?? '');
    }
    return $project[$key] ?? $fallback;
};
?>
<form class="grid gap-6 lg:grid-cols-3" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="space-y-4 lg:col-span-2">
        <div>
            <label class="field-label" for="name">Name</label>
            <input class="field-input" id="name" name="name" required maxlength="160" data-slug-source value="<?= e((string) $value('name')) ?>">
            <?php if (!empty($errors['name'])): ?><p class="field-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>
        <div>
            <label class="field-label" for="slug">Slug</label>
            <input class="field-input font-mono" id="slug" name="slug" required maxlength="180" data-slug-target value="<?= e((string) $value('slug')) ?>">
            <?php if (!empty($errors['slug'])): ?><p class="field-error"><?= e($errors['slug']) ?></p><?php endif; ?>
        </div>
        <div>
            <label class="field-label" for="short_description">Short description <span data-counter>0</span>/300</label>
            <textarea class="field-input" id="short_description" name="short_description" required maxlength="300" data-count="300"><?= e((string) $value('short_description')) ?></textarea>
            <?php if (!empty($errors['short_description'])): ?><p class="field-error"><?= e($errors['short_description']) ?></p><?php endif; ?>
        </div>
        <div>
            <label class="field-label" for="long_description">Long description</label>
            <textarea class="field-input min-h-36" id="long_description" name="long_description"><?= e((string) $value('long_description')) ?></textarea>
        </div>
        <div>
            <label class="field-label" for="url">Project link</label>
            <div class="flex gap-2">
                <input class="field-input" id="url" name="url" type="url" required placeholder="https://" value="<?= e((string) $value('url')) ?>">
                <a class="btn-ghost" data-url-preview href="<?= e((string) $value('url', '#')) ?>" target="_blank" rel="noopener noreferrer">Preview</a>
            </div>
            <?php if (!empty($errors['url'])): ?><p class="field-error"><?= e($errors['url']) ?></p><?php endif; ?>
        </div>
        <div>
            <label class="field-label" for="technologies">Technologies</label>
            <input class="field-input" id="technologies" name="technologies" placeholder="PHP, MySQL" value="<?= e((string) $value('technologies')) ?>">
        </div>
    </div>
    <div class="space-y-4">
        <div>
            <label class="field-label" for="category_id">Category</label>
            <select class="field-input" id="category_id" name="category_id">
                <option value="">None</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e((string) $category['id']) ?>"<?= (string) $value('category_id') === (string) $category['id'] ? ' selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="field-label mt-3" for="new_category">Or create a category</label>
            <input class="field-input" id="new_category" name="new_category" maxlength="80" value="<?= e((string) $value('new_category')) ?>">
        </div>
        <div>
            <label class="field-label" for="year">Year</label>
            <input class="field-input" id="year" name="year" inputmode="numeric" maxlength="4" value="<?= e((string) $value('year')) ?>">
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select class="field-input" id="status" name="status">
                <?php foreach (['published', 'draft'] as $status): ?>
                    <option value="<?= e($status) ?>"<?= (string) $value('status', 'published') === $status ? ' selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1"<?= (string) $value('featured', '0') === '1' || $value('featured') === 1 ? ' checked' : '' ?>> Featured</label>
        <?php if ($editing): ?>
            <div>
                <label class="field-label" for="images">Add images</label>
                <input class="field-input" id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            </div>
        <?php endif; ?>
        <button class="btn-primary w-full" type="submit"><?= $editing ? 'Save project' : 'Create project' ?></button>
    </div>
</form>
<?php if ($editing): ?>
<form class="mt-4 max-w-xs" method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/delete')) ?>" data-confirm="Delete this project, its database rows, and its files?">
    <?= csrf_field() ?>
    <button class="btn-ghost w-full" type="submit">Delete project</button>
</form>
<?php endif; ?>
<?php if ($editing): ?>
<section class="mt-10" data-uploader data-upload-url="<?= e(url('/admin/projects/' . $project['id'] . '/images')) ?>" data-reorder-url="<?= e(url('/admin/projects/' . $project['id'] . '/images/reorder')) ?>">
    <h2 class="font-display text-xl font-semibold">Pictures</h2>
    <p class="mt-1 text-sm text-muted">Drop images here. JPEG, PNG, WebP, or GIF. 8MB each.</p>
    <div class="dropzone mt-4" data-dropzone tabindex="0">Drop images or click to browse</div>
    <div class="mt-2 h-2 overflow-hidden rounded-full bg-surface2"><div class="h-full w-0 bg-accent" data-progress></div></div>
    <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-image-list>
        <?php foreach ($images as $image): ?>
            <li class="glass p-3" draggable="true" data-id="<?= e((string) $image['id']) ?>">
                <img src="<?= e(\App\Services\Media::baseUrl((int) $project['id'], (string) $image['stem']) . '-thumb.webp') ?>" alt="<?= e($image['alt_text']) ?>" class="aspect-video w-full rounded-xl object-cover">
                <form class="mt-3" method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/images/' . $image['id'] . '/alt')) ?>">
                    <?= csrf_field() ?>
                    <label class="field-label" for="alt-<?= e((string) $image['id']) ?>">Alt text</label>
                    <input class="field-input" id="alt-<?= e((string) $image['id']) ?>" name="alt_text" value="<?= e($image['alt_text']) ?>">
                    <button class="mt-2 text-sm text-accent" type="submit">Save alt</button>
                </form>
                <div class="mt-3 flex gap-3 text-sm">
                    <?php if ((int) $image['is_cover'] === 1): ?>
                        <span class="text-accent">Cover</span>
                    <?php else: ?>
                        <form method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/images/' . $image['id'] . '/cover')) ?>"><?= csrf_field() ?><button class="text-accent" type="submit">Make cover</button></form>
                    <?php endif; ?>
                    <form method="post" action="<?= e(url('/admin/projects/' . $project['id'] . '/images/' . $image['id'] . '/delete')) ?>" data-confirm="Delete this image file?"><?= csrf_field() ?><button type="submit">Delete</button></form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
