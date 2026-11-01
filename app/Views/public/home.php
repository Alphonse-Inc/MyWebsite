<?php
/** @var array<string, mixed> $profile */
/** @var array<string, string> $settings */
$titles = $profile['titles'] ?? [];
$photo = (string) ($profile['photo_path'] ?? '');
$video = (string) ($profile['hero_video_path'] ?? '');
$poster = (string) ($profile['hero_poster_path'] ?? '');
?>
<section class="hero" id="top">
    <?php if ($video !== ''): ?>
        <div class="hero-media" data-hero-media>
            <video class="hero-video" muted loop playsinline poster="<?= e($poster !== '' ? '/uploads/' . $poster . '-large.jpg' : '') ?>" data-src="<?= e('/uploads/' . $video) ?>"></video>
            <div class="hero-shade"></div>
        </div>
    <?php endif; ?>
    <div class="mx-auto grid min-h-[calc(100vh-4.5rem)] max-w-6xl items-center gap-12 px-4 py-16 md:px-6 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <?php if ((int) ($profile['available'] ?? 0) === 1): ?>
                <p class="pill"><span class="pulse-dot" aria-hidden="true"></span><?= e((string) $profile['availability_label']) ?></p>
            <?php endif; ?>
            <?php
            $displayName = (string) $profile['display_name'];
            $nameParts = preg_split('/\s+/', trim($displayName)) ?: [$displayName];
            $lastName = array_pop($nameParts);
            $firstNames = implode(' ', $nameParts);
            ?>
            <h1 class="hero-title font-display font-semibold tracking-tight">
                <?= e($firstNames) ?> <span class="text-gradient"><?= e((string) $lastName) ?></span>
            </h1>
            <p class="typed font-mono text-accent" data-typed='<?= e(json_encode(array_values($titles), JSON_UNESCAPED_UNICODE)) ?>'>
                <span data-typed-text><?= e($titles[0] ?? '') ?></span><span class="typed-cursor" aria-hidden="true"></span>
            </p>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted"><?= e((string) $profile['tagline']) ?></p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a class="btn-primary" data-magnetic href="<?= e(url('/cv')) ?>"><?= icon('download', 'h-4 w-4') ?><span><?= e(setting($settings, 'btn_download_cv', 'Download CV')) ?></span></a>
                <a class="btn-ghost" data-magnetic href="#contact"><?= e(setting($settings, 'btn_contact', 'Contact Me')) ?></a>
            </div>
            <ul class="mt-8 flex items-center gap-3">
                <?php foreach ($socials as $social): ?>
                    <li>
                        <a class="icon-btn" href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>">
                            <?= icon($social['platform'] === 'x' ? 'x-social' : (string) $social['platform'], 'h-5 w-5') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="lg:col-span-5" data-parallax>
            <div class="portrait-ring">
                <?php if ($photo !== ''): ?>
                    <?php render_upload($photo, $profile['photo_meta'] ?? null, (string) ($profile['photo_alt'] ?? $profile['display_name']), 'portrait-photo', true); ?>
                <?php else: ?>
                    <div class="portrait-fallback">EA</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-line bg-surface/60">
    <div class="mx-auto grid max-w-6xl grid-cols-3 px-4 py-8 md:px-6">
        <div>
            <p class="stat-value font-display" data-count="<?= e((string) $stats['years']) ?>">0</p>
            <p class="stat-label">Years in IT</p>
        </div>
        <div>
            <p class="stat-value font-display" data-count="<?= e((string) $stats['projects']) ?>">0</p>
            <p class="stat-label"><?= e(setting($settings, 'label_projects', 'Projects')) ?></p>
        </div>
        <div>
            <p class="stat-value font-display"><span data-count="<?= e((string) $stats['remedy']) ?>">0</span>%</p>
            <p class="stat-label">Incident remedy</p>
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 md:px-6 lg:grid-cols-12">
        <div class="reveal lg:col-span-7">
            <p class="eyebrow"><?= e(setting($settings, 'label_about', 'About')) ?></p>
            <h2 class="section-title">About <span class="text-gradient">me</span></h2>
            <div class="mt-6 space-y-4 text-lg leading-relaxed text-muted">
                <?php foreach (preg_split("/\n{2,}/", trim((string) $profile['bio'])) ?: [] as $paragraph): ?>
                    <p><?= e(trim($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <aside class="reveal glass glass-hover p-8 lg:col-span-5">
            <p class="eyebrow"><?= e(setting($settings, 'label_quote', 'Quote')) ?></p>
            <blockquote class="mt-4 font-display text-2xl font-semibold leading-snug">“<?= e((string) $profile['quote']) ?>”</blockquote>
            <p class="mt-6 font-mono text-sm text-accent"><?= e((string) $profile['quote_author']) ?></p>
        </aside>
    </div>
</section>

<section class="section pt-0" id="services">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
        <p class="eyebrow"><?= e(setting($settings, 'label_services', 'My Services')) ?></p>
        <h2 class="section-title">How I can <span class="text-gradient">help</span></h2>
        <div class="mt-10 grid gap-4 md:grid-cols-2">
            <?php foreach ($services as $service): ?>
                <article class="reveal glass glass-hover p-6">
                    <div class="icon-badge"><?= icon((string) $service['icon'], 'h-5 w-5') ?></div>
                    <h3 class="mt-4 font-display text-xl font-semibold"><?= e($service['name']) ?></h3>
                    <p class="mt-2 leading-relaxed text-muted"><?= e($service['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section pt-0" id="resume">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><?= e(setting($settings, 'label_resume', 'Resume')) ?></p>
                <h2 class="section-title">Experience and <span class="text-gradient">education</span></h2>
            </div>
            <a class="btn-ghost no-print" href="<?= e(url('/resume')) ?>">Full resume</a>
        </div>
        <div class="mt-10 grid gap-10 lg:grid-cols-2">
            <div>
                <h3 class="mb-4 font-mono text-xs uppercase tracking-[0.2em] text-accent"><?= e(setting($settings, 'label_experience', 'Experience')) ?></h3>
                <ol class="timeline">
                    <?php foreach ($experiences as $item): ?>
                        <li class="timeline-item reveal">
                            <p class="font-mono text-xs text-accent"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                            <h4 class="mt-1 font-display text-lg font-semibold"><?= e($item['role']) ?></h4>
                            <p class="text-sm text-muted"><?= e($item['organization']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                            <p class="mt-2 text-sm leading-relaxed text-muted"><?= e($item['description']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <div>
                <h3 class="mb-4 font-mono text-xs uppercase tracking-[0.2em] text-accent"><?= e(setting($settings, 'label_education', 'Education')) ?></h3>
                <ol class="timeline">
                    <?php foreach ($education as $item): ?>
                        <li class="timeline-item reveal">
                            <p class="font-mono text-xs text-accent"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                            <h4 class="mt-1 font-display text-lg font-semibold"><?= e($item['title']) ?></h4>
                            <p class="text-sm text-muted"><?= e($item['institution']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                            <?php if ($item['description'] !== ''): ?><p class="mt-2 text-sm leading-relaxed text-muted"><?= e($item['description']) ?></p><?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="section pt-0" id="skills">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
        <p class="eyebrow"><?= e(setting($settings, 'label_skills', 'My Skills')) ?></p>
        <h2 class="section-title">Tools I <span class="text-gradient">use</span></h2>
        <div class="mt-10 grid gap-4 md:grid-cols-2">
            <?php foreach ($skillGroups as $group): ?>
                <article class="glass p-6">
                    <h3 class="font-display text-lg font-semibold"><?= e($group['name']) ?></h3>
                    <?php if ($group['display_type'] === 'tag'): ?>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li class="tag"><?= e($skill['name']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif ($group['display_type'] === 'ring'): ?>
                        <ul class="mt-4 grid grid-cols-2 gap-4">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li class="text-center">
                                    <div class="ring" style="--value: <?= e((string) (int) $skill['level']) ?>" data-ring="<?= e((string) (int) $skill['level']) ?>">
                                        <span><?= e((string) (int) $skill['level']) ?>%</span>
                                    </div>
                                    <p class="mt-2 text-sm"><?= e($skill['name']) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <ul class="mt-4 space-y-4">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li>
                                    <div class="mb-1 flex justify-between text-sm"><span><?= e($skill['name']) ?></span><span class="font-mono text-accent"><?= e((string) (int) $skill['level']) ?></span></div>
                                    <div class="bar"><span data-bar="<?= e((string) (int) $skill['level']) ?>"></span></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section pt-0" id="projects">
    <div class="mx-auto max-w-6xl px-4 md:px-6" x-data="{ cat: 'all' }">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="eyebrow"><?= e(setting($settings, 'label_projects', 'Projects')) ?></p>
                <h2 class="section-title">Selected <span class="text-gradient">work</span></h2>
            </div>
            <div class="flex flex-wrap gap-2" role="tablist" aria-label="Filter projects">
                <button type="button" class="filter-btn" :class="cat === 'all' && 'is-on'" @click="cat = 'all'">All</button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="filter-btn" :class="cat === '<?= e($category['slug']) ?>' && 'is-on'" @click="cat = '<?= e($category['slug']) ?>'"><?= e($category['name']) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($projects as $project): ?>
                <a class="project-card reveal" data-tilt href="<?= e(url('/projects/' . $project['slug'])) ?>" x-show="cat === 'all' || cat === '<?= e((string) ($project['category_slug'] ?? '')) ?>'" x-transition.opacity>
                    <div class="project-media">
                        <?php if (!empty($project['cover'])): ?>
                            <?php render_project_image($project['cover'], (int) $project['id'], (string) ($project['cover']['alt_text'] ?: $project['name']), 'h-full w-full object-cover'); ?>
                        <?php else: ?>
                            <div class="grid h-full place-items-center text-sm text-muted"><?= e($project['name']) ?></div>
                        <?php endif; ?>
                        <span class="project-overlay"><?= e(setting($settings, 'btn_view_project', 'View project')) ?></span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-display text-lg font-semibold"><?= e($project['name']) ?></h3>
                        <p class="mt-1 font-mono text-xs text-accent"><?= e((string) ($project['category_name'] ?? '')) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="section pt-0" id="testimonials">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
        <h2 class="section-title">Notes from <span class="text-gradient">clients</span></h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2">
            <?php foreach ($testimonials as $item): ?>
                <blockquote class="glass p-6">
                    <p class="leading-relaxed"><?= e($item['quote']) ?></p>
                    <footer class="mt-4 font-mono text-sm text-accent"><?= e($item['author_name']) ?><?= $item['author_role'] !== '' ? ' · ' . e($item['author_role']) : '' ?></footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section pt-0" id="contact">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 md:px-6 lg:grid-cols-2">
        <div>
            <p class="eyebrow"><?= e(setting($settings, 'label_contact', 'Contact')) ?></p>
            <h2 class="section-title"><?= e(setting($settings, 'label_get_in_touch', 'Get in Touch')) ?></h2>
            <?php partial('partials/contact-details', compact('settings', 'profile')); ?>
        </div>
        <div class="glass p-6 md:p-8">
            <?php $compact = false; partial('partials/contact-form', compact('settings', 'compact')); ?>
        </div>
    </div>
</section>
