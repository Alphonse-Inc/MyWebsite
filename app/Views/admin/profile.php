<?php $p = $profile; $titles = implode("\n", $p['titles'] ?? []); ?>
<form class="grid max-w-3xl gap-4" method="post" action="<?= e(url('/admin/profile')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label class="field-label" for="full_name">Full name</label>
    <input class="field-input" id="full_name" name="full_name" required value="<?= e($p['full_name']) ?>">
    <label class="field-label" for="display_name">Display name</label>
    <input class="field-input" id="display_name" name="display_name" required value="<?= e($p['display_name']) ?>">
    <label class="field-label" for="handle">Handle</label>
    <input class="field-input" id="handle" name="handle" value="<?= e($p['handle']) ?>">
    <label class="field-label" for="titles">Typed titles, one per line</label>
    <textarea class="field-input min-h-28" id="titles" name="titles" required><?= e($titles) ?></textarea>
    <label class="field-label" for="tagline">Tagline</label>
    <input class="field-input" id="tagline" name="tagline" value="<?= e($p['tagline']) ?>">
    <label class="field-label" for="bio">Bio</label>
    <textarea class="field-input min-h-36" id="bio" name="bio" required><?= e($p['bio']) ?></textarea>
    <label class="field-label" for="quote">Quote</label>
    <textarea class="field-input" id="quote" name="quote" required><?= e($p['quote']) ?></textarea>
    <label class="field-label" for="quote_author">Quote author</label>
    <input class="field-input" id="quote_author" name="quote_author" value="<?= e($p['quote_author']) ?>">
    <label class="field-label" for="availability_label">Availability label</label>
    <input class="field-input" id="availability_label" name="availability_label" value="<?= e($p['availability_label']) ?>">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="available" value="1"<?= (int) $p['available'] === 1 ? ' checked' : '' ?>> Show availability pill</label>
    <label class="field-label" for="photo_alt">Photo alt text</label>
    <input class="field-input" id="photo_alt" name="photo_alt" value="<?= e((string) $p['photo_alt']) ?>">
    <label class="field-label" for="photo">Replace portrait</label>
    <input class="field-input" id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
    <label class="field-label" for="poster">Hero poster</label>
    <input class="field-input" id="poster" name="poster" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
    <label class="field-label" for="video">Hero video (MP4)</label>
    <input class="field-input" id="video" name="video" type="file" accept="video/mp4">
    <?php if (!empty($p['hero_video_path'])): ?><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remove_video" value="1"> Remove current video</label><?php endif; ?>
    <button class="btn-primary" type="submit">Save profile</button>
</form>
