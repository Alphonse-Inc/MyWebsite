<article class="section resume-sheet">
    <div class="mx-auto max-w-4xl px-4 md:px-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="eyebrow no-print"><?= e(setting($settings, 'label_resume', 'Resume')) ?></p>
                <h1 class="section-title"><?= e((string) $profile['full_name']) ?></h1>
                <p class="mt-2 font-mono text-sm text-accent"><?= e(implode(' · ', $profile['titles'] ?? [])) ?></p>
                <p class="mt-4 max-w-2xl leading-relaxed text-muted"><?= e((string) $profile['bio']) ?></p>
            </div>
            <a class="btn-primary no-print" data-magnetic href="<?= e(url('/cv')) ?>"><?= icon('download', 'h-4 w-4') ?><span><?= e(setting($settings, 'btn_download_cv', 'Download CV')) ?></span></a>
        </div>
        <p class="mt-4 text-sm text-muted"><?= e(setting($settings, 'contact_location')) ?> · <a class="text-accent" href="mailto:<?= e(setting($settings, 'contact_email')) ?>"><?= e(setting($settings, 'contact_email')) ?></a> · <?= e(setting($settings, 'contact_phone')) ?></p>
        <h2 class="mt-12 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_experience', 'Experience')) ?></h2>
        <ol class="timeline mt-6">
            <?php foreach ($experiences as $item): ?>
                <li class="timeline-item">
                    <p class="font-mono text-xs text-accent"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                    <h3 class="mt-1 font-display text-xl font-semibold"><?= e($item['role']) ?></h3>
                    <p class="text-sm text-muted"><?= e($item['organization']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                    <p class="mt-2 leading-relaxed text-muted"><?= e($item['description']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
        <h2 class="mt-12 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_education', 'Education')) ?></h2>
        <ol class="timeline mt-6">
            <?php foreach ($education as $item): ?>
                <li class="timeline-item">
                    <p class="font-mono text-xs text-accent"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                    <h3 class="mt-1 font-display text-xl font-semibold"><?= e($item['title']) ?></h3>
                    <p class="text-sm text-muted"><?= e($item['institution']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                    <?php if ($item['description'] !== ''): ?><p class="mt-2 leading-relaxed text-muted"><?= e($item['description']) ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
        <h2 class="mt-12 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_skills', 'My Skills')) ?></h2>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <?php foreach ($skillGroups as $group): ?>
                <section>
                    <h3 class="font-display font-semibold"><?= e($group['name']) ?></h3>
                    <ul class="mt-2 space-y-1 text-sm text-muted">
                        <?php foreach ($group['skills'] as $skill): ?>
                            <li><?= e($skill['name']) ?><?= $skill['level'] !== null ? ' — ' . e((string) $skill['level']) : '' ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</article>
