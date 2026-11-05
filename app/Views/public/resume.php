<article class="section resume-sheet">
    <div class="pub-wrap" style="max-width:52rem">
        <div class="pub-resume-head">
            <div>
                <p class="eyebrow no-print"><?= e(setting($settings, 'label_resume', 'Resume')) ?></p>
                <h1 class="section-title"><?= e((string) $profile['full_name']) ?></h1>
                <p class="mt-3 font-mono text-sm text-accent"><?= e(implode(' · ', $profile['titles'] ?? [])) ?></p>
                <p class="pub-lede"><?= e((string) $profile['bio']) ?></p>
            </div>
            <a class="btn-primary no-print" data-magnetic href="<?= e(url('/cv')) ?>"><?= icon('download', 'h-4 w-4') ?><span><?= e(setting($settings, 'btn_download_cv', 'Download CV')) ?></span></a>
        </div>
        <p class="text-sm text-muted"><?= e(setting($settings, 'contact_location')) ?> · <a class="text-accent" href="mailto:<?= e(setting($settings, 'contact_email')) ?>"><?= e(setting($settings, 'contact_email')) ?></a> · <?= e(setting($settings, 'contact_phone')) ?></p>
        <h2 class="mt-16 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_experience', 'Experience')) ?></h2>
        <div class="timeline-wrap mt-8">
            <div class="timeline-line" aria-hidden="true"></div>
            <ol>
                <?php foreach ($experiences as $item): ?>
                    <li class="timeline-item">
                        <p class="pub-tl-date"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                        <h3 class="pub-role"><?= e($item['role']) ?></h3>
                        <p class="pub-meta"><?= e($item['organization']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                        <p class="pub-body"><?= e($item['description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <h2 class="mt-12 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_education', 'Education')) ?></h2>
        <div class="timeline-wrap mt-8">
            <div class="timeline-line" aria-hidden="true"></div>
            <ol>
                <?php foreach ($education as $item): ?>
                    <li class="timeline-item">
                        <p class="pub-tl-date"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                        <h3 class="pub-role"><?= e($item['title']) ?></h3>
                        <p class="pub-meta"><?= e($item['institution']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                        <?php if ($item['description'] !== ''): ?><p class="pub-body"><?= e($item['description']) ?></p><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <h2 class="mt-12 font-display text-2xl font-semibold"><?= e(setting($settings, 'label_skills', 'My Skills')) ?></h2>
        <div class="mt-8 grid gap-8 md:grid-cols-2">
            <?php foreach ($skillGroups as $group): ?>
                <section>
                    <h3 class="font-display font-semibold"><?= e($group['name']) ?></h3>
                    <ul class="mt-3 space-y-1 text-sm text-muted">
                        <?php foreach ($group['skills'] as $skill): ?>
                            <li><?= e($skill['name']) ?><?= $skill['level'] !== null ? ' — ' . e((string) $skill['level']) : '' ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</article>
