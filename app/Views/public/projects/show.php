<?php
/** @var array<string, mixed> $project */
/** @var list<array<string, mixed>> $images */
$neighbors = $neighbors ?? ['prev' => null, 'next' => null];
?>
<article class="section">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
        <a class="font-mono text-sm text-accent" href="<?= e(url('/projects')) ?>">← <?= e(setting($settings, 'label_projects', 'Projects')) ?></a>
        <header class="mt-4 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <p class="eyebrow"><?= e((string) ($project['category_name'] ?? '')) ?><?= $project['year'] ? ' · ' . e((string) $project['year']) : '' ?></p>
                <h1 class="section-title"><?= e($project['name']) ?></h1>
                <p class="mt-4 max-w-2xl text-lg leading-relaxed text-muted"><?= e($project['short_description']) ?></p>
            </div>
            <a class="btn-primary" data-magnetic href="<?= e($project['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e(setting($settings, 'btn_visit', 'Visit project')) ?> <?= icon('arrow-up-right', 'h-4 w-4') ?></a>
        </header>
        <?php if ($project['technologies'] !== []): ?>
            <ul class="mt-6 flex flex-wrap gap-2">
                <?php foreach ($project['technologies'] as $tech): ?>
                    <li class="tag"><?= e($tech) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($images !== []): ?>
            <div class="swiper project-swiper mt-8 overflow-hidden rounded-2xl border border-line">
                <div class="swiper-wrapper">
                    <?php foreach ($images as $index => $image): ?>
                        <div class="swiper-slide">
                            <button type="button" class="block w-full" data-lightbox-open="<?= e((string) $index) ?>" aria-label="Open image <?= e((string) ($index + 1)) ?>">
                                <?php render_project_image($image, (int) $project['id'], (string) ($image['alt_text'] ?: $project['name']), 'aspect-[16/10] w-full object-cover', $index === 0, '100vw'); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <dialog class="lightbox" id="lightbox" aria-label="Project gallery">
                <button type="button" class="lightbox-close icon-btn" data-lightbox-close aria-label="Close"><?= icon('x', 'h-5 w-5') ?></button>
                <div class="swiper lightbox-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($images as $image): ?>
                            <div class="swiper-slide grid place-items-center">
                                <?php render_project_image($image, (int) $project['id'], (string) ($image['alt_text'] ?: $project['name']), 'max-h-[80vh] w-auto rounded-xl', true, '100vw'); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </dialog>
        <?php endif; ?>
        <?php if (!empty($project['long_description'])): ?>
            <div class="prose-block mt-8 max-w-3xl">
                <?php foreach (preg_split("/\n{2,}/", trim((string) $project['long_description'])) ?: [] as $paragraph): ?>
                    <p><?= e(trim($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <nav class="mt-10 flex justify-between gap-4 border-t border-line pt-6" aria-label="More projects">
            <?php if (!empty($neighbors['prev'])): ?>
                <a class="text-accent" href="<?= e(url('/projects/' . $neighbors['prev']['slug'])) ?>">← <?= e($neighbors['prev']['name']) ?></a>
            <?php else: ?><span></span><?php endif; ?>
            <?php if (!empty($neighbors['next'])): ?>
                <a class="text-accent" href="<?= e(url('/projects/' . $neighbors['next']['slug'])) ?>"><?= e($neighbors['next']['name']) ?> →</a>
            <?php endif; ?>
        </nav>
    </div>
</article>
