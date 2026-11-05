<?php
/** @var array<string, mixed> $profile */
/** @var array<string, string> $settings */
$titles = $profile['titles'] ?? [];
$photo = (string) ($profile['photo_path'] ?? '');
$video = (string) ($profile['hero_video_path'] ?? '');
$poster = (string) ($profile['hero_poster_path'] ?? '');
$displayName = (string) $profile['display_name'];
$nameParts = preg_split('/\s+/', trim($displayName)) ?: [$displayName];
$lastName = array_pop($nameParts);
$firstNames = implode(' ', $nameParts);
?>
<section class="hero" id="top">
    <?php if ($video !== ''): ?>
        <div class="hero-media" data-hero-media>
            <video class="hero-video" muted loop playsinline poster="<?= e($poster !== '' ? '/uploads/' . $poster . '-large.jpg' : '') ?>" data-src="<?= e('/uploads/' . $video) ?>"></video>
            <div class="hero-shade"></div>
        </div>
    <?php endif; ?>
    <div class="pub-wrap pub-hero-grid">
        <div class="pub-hero-copy">
            <?php if ((int) ($profile['available'] ?? 0) === 1): ?>
                <p class="pill"><span class="pulse-dot" aria-hidden="true"></span><?= e((string) $profile['availability_label']) ?></p>
            <?php endif; ?>
            <h1 class="hero-title font-display">
                <?= e($firstNames) ?> <span class="text-gradient"><?= e((string) $lastName) ?></span>
            </h1>
            <p class="typed font-mono text-accent" data-typed='<?= e(json_encode(array_values($titles), JSON_UNESCAPED_UNICODE)) ?>'>
                <span data-typed-text><?= e($titles[0] ?? '') ?></span><span class="typed-cursor" aria-hidden="true"></span>
            </p>
            <p class="pub-lede"><?= e((string) $profile['tagline']) ?></p>
            <div class="pub-actions">
                <a class="btn-primary" data-magnetic href="<?= e(url('/cv')) ?>"><?= icon('download', 'h-4 w-4') ?><span><?= e(setting($settings, 'btn_download_cv', 'Download CV')) ?></span></a>
                <a class="btn-ghost" data-magnetic href="#contact"><?= e(setting($settings, 'btn_contact', 'Contact Me')) ?></a>
            </div>
            <ul class="pub-socials">
                <?php foreach ($socials as $social): ?>
                    <li>
                        <a class="icon-btn" href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>">
                            <?= icon($social['platform'] === 'x' ? 'x-social' : (string) $social['platform'], 'h-5 w-5') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div data-parallax>
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

<section>
    <div class="pub-wrap">
        <div class="pub-stats">
            <div class="pub-stat">
                <p class="stat-value font-display" data-count="<?= e((string) $stats['years']) ?>">0</p>
                <p class="stat-label">Years in IT</p>
            </div>
            <div class="pub-stat">
                <p class="stat-value font-display" data-count="<?= e((string) $stats['projects']) ?>">0</p>
                <p class="stat-label"><?= e(setting($settings, 'label_projects', 'Projects')) ?></p>
            </div>
            <div class="pub-stat">
                <p class="stat-value font-display"><span data-count="<?= e((string) $stats['remedy']) ?>">0</span>%</p>
                <p class="stat-label">Incident remedy</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="pub-wrap pub-split">
        <div class="reveal">
            <div class="pub-section-head">
                <p class="eyebrow"><?= e(setting($settings, 'label_about', 'About')) ?></p>
                <h2 class="section-title">About <span class="text-gradient">me</span></h2>
            </div>
            <div class="pub-about space-y-4">
                <?php foreach (preg_split("/\n{2,}/", trim((string) $profile['bio'])) ?: [] as $paragraph): ?>
                    <p><?= e(trim($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <aside class="pub-quote reveal">
            <p class="eyebrow"><?= e(setting($settings, 'label_quote', 'Quote')) ?></p>
            <blockquote>“<?= e((string) $profile['quote']) ?>”</blockquote>
            <p class="mt-6 font-mono text-sm text-accent"><?= e((string) $profile['quote_author']) ?></p>
        </aside>
    </div>
</section>

<section class="section" id="services">
    <div class="pub-wrap">
        <div class="pub-section-head">
            <p class="eyebrow"><?= e(setting($settings, 'label_services', 'My Services')) ?></p>
            <h2 class="section-title">How I can <span class="text-gradient">help</span></h2>
        </div>
        <div class="pub-services">
            <?php foreach ($services as $index => $service): ?>
                <article class="pub-service reveal">
                    <span class="pub-index"><?= e(sprintf('%02d', $index + 1)) ?></span>
                    <div>
                        <div class="pub-service-title">
                            <span class="icon-badge"><?= icon((string) $service['icon'], 'h-5 w-5') ?></span>
                            <h3><?= e($service['name']) ?></h3>
                        </div>
                        <p><?= e($service['description']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="resume">
    <div class="pub-wrap">
        <div class="pub-resume-head">
            <div class="pub-section-head" style="margin-bottom:0">
                <p class="eyebrow"><?= e(setting($settings, 'label_resume', 'Resume')) ?></p>
                <h2 class="section-title">Experience and <span class="text-gradient">education</span></h2>
            </div>
            <a class="btn-ghost no-print" href="<?= e(url('/resume')) ?>">Full resume</a>
        </div>
        <div class="pub-columns">
            <div>
                <h3 class="pub-kicker"><?= e(setting($settings, 'label_experience', 'Experience')) ?></h3>
                <div class="timeline-wrap">
                    <div class="timeline-line" aria-hidden="true"></div>
                    <ol>
                        <?php foreach ($experiences as $item): ?>
                            <li class="timeline-item reveal">
                                <p class="pub-tl-date"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                                <h4 class="pub-role"><?= e($item['role']) ?></h4>
                                <p class="pub-meta"><?= e($item['organization']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                                <p class="pub-body"><?= e($item['description']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <div>
                <h3 class="pub-kicker"><?= e(setting($settings, 'label_education', 'Education')) ?></h3>
                <div class="timeline-wrap">
                    <div class="timeline-line" aria-hidden="true"></div>
                    <ol>
                        <?php foreach ($education as $item): ?>
                            <li class="timeline-item reveal">
                                <p class="pub-tl-date"><?= e($item['start_label']) ?> — <?= e($item['end_label']) ?></p>
                                <h4 class="pub-role"><?= e($item['title']) ?></h4>
                                <p class="pub-meta"><?= e($item['institution']) ?><?= $item['location'] !== '' ? ' · ' . e($item['location']) : '' ?></p>
                                <?php if ($item['description'] !== ''): ?><p class="pub-body"><?= e($item['description']) ?></p><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="skills">
    <div class="pub-wrap">
        <div class="pub-section-head">
            <p class="eyebrow"><?= e(setting($settings, 'label_skills', 'My Skills')) ?></p>
            <h2 class="section-title">Tools I <span class="text-gradient">use</span></h2>
        </div>
        <div class="pub-skill-grid">
            <?php foreach ($skillGroups as $group): ?>
                <article class="glass pub-skill">
                    <h3 class="font-display text-lg font-semibold"><?= e($group['name']) ?></h3>
                    <?php if ($group['display_type'] === 'tag'): ?>
                        <ul class="mt-5 flex flex-wrap gap-2">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li class="tag"><?= e($skill['name']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif ($group['display_type'] === 'ring'): ?>
                        <ul class="mt-6 grid grid-cols-2 gap-6">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li class="text-center">
                                    <div class="skill-ring" style="--value: <?= e((string) (int) $skill['level']) ?>" data-ring="<?= e((string) (int) $skill['level']) ?>">
                                        <span><?= e((string) (int) $skill['level']) ?>%</span>
                                    </div>
                                    <p class="mt-3 text-sm"><?= e($skill['name']) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <ul class="mt-6 space-y-5">
                            <?php foreach ($group['skills'] as $skill): ?>
                                <li>
                                    <div class="mb-2 flex justify-between text-sm"><span><?= e($skill['name']) ?></span><span class="font-mono text-accent"><?= e((string) (int) $skill['level']) ?></span></div>
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

<section class="section" id="projects">
    <div class="pub-wrap" x-data="{ cat: 'all' }">
        <div class="pub-resume-head">
            <div class="pub-section-head" style="margin-bottom:0">
                <p class="eyebrow"><?= e(setting($settings, 'label_projects', 'Projects')) ?></p>
                <h2 class="section-title">Selected <span class="text-gradient">work</span></h2>
            </div>
            <div class="pub-filters" role="tablist" aria-label="Filter projects">
                <button type="button" class="filter-btn" :class="cat === 'all' && 'is-on'" @click="cat = 'all'">All</button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="filter-btn" :class="cat === '<?= e($category['slug']) ?>' && 'is-on'" @click="cat = '<?= e($category['slug']) ?>'"><?= e($category['name']) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="pub-project-grid">
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
                    <div class="pub-project-copy">
                        <h3><?= e($project['name']) ?></h3>
                        <p class="mt-1 font-mono text-xs text-accent"><?= e((string) ($project['category_name'] ?? '')) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="section" id="testimonials">
    <div class="pub-wrap">
        <h2 class="section-title">Notes from <span class="text-gradient">clients</span></h2>
        <div class="pub-project-grid">
            <?php foreach ($testimonials as $item): ?>
                <blockquote class="glass p-8">
                    <p class="leading-relaxed"><?= e($item['quote']) ?></p>
                    <footer class="mt-4 font-mono text-sm text-accent"><?= e($item['author_name']) ?><?= $item['author_role'] !== '' ? ' · ' . e($item['author_role']) : '' ?></footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section" id="contact">
    <div class="pub-wrap pub-contact">
        <div>
            <div class="pub-section-head">
                <p class="eyebrow"><?= e(setting($settings, 'label_contact', 'Contact')) ?></p>
                <h2 class="section-title"><?= e(setting($settings, 'label_get_in_touch', 'Get in Touch')) ?></h2>
            </div>
            <?php partial('partials/contact-details', compact('settings', 'profile')); ?>
        </div>
        <div class="glass pub-panel">
            <?php $compact = false; partial('partials/contact-form', compact('settings', 'compact')); ?>
        </div>
    </div>
</section>
