<section class="section">
    <div class="pub-wrap" x-data="{ cat: 'all' }">
        <div class="pub-page-intro">
            <p class="eyebrow"><?= e(setting($settings, 'label_projects', 'Projects')) ?></p>
            <h1 class="section-title">Selected <span class="text-gradient">work</span></h1>
        </div>
        <div class="pub-filters">
            <button type="button" class="filter-btn" :class="cat === 'all' && 'is-on'" @click="cat = 'all'">All</button>
            <?php foreach ($categories as $category): ?>
                <button type="button" class="filter-btn" :class="cat === '<?= e($category['slug']) ?>' && 'is-on'" @click="cat = '<?= e($category['slug']) ?>'"><?= e($category['name']) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="pub-project-grid">
            <?php foreach ($projects as $project): ?>
                <a class="project-card" data-tilt href="<?= e(url('/projects/' . $project['slug'])) ?>" x-show="cat === 'all' || cat === '<?= e((string) ($project['category_slug'] ?? '')) ?>'" x-transition.opacity>
                    <div class="project-media">
                        <?php if (!empty($project['cover'])): ?>
                            <?php render_project_image($project['cover'], (int) $project['id'], (string) ($project['cover']['alt_text'] ?: $project['name']), 'h-full w-full object-cover'); ?>
                        <?php endif; ?>
                        <span class="project-overlay"><?= e(setting($settings, 'btn_view_project', 'View project')) ?></span>
                    </div>
                    <div class="pub-project-copy">
                        <h2><?= e($project['name']) ?></h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted"><?= e($project['short_description']) ?></p>
                        <p class="mt-3 font-mono text-xs text-accent"><?= e((string) ($project['category_name'] ?? '')) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
