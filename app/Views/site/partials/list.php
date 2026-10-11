<section class="mt-8">
    <h2 class="text-lg font-bold border-b border-base-content/15 pb-2 mb-4">
        <?= esc($collection['name']) ?>
    </h2>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($collection['links'] as $link): ?>
            <?php $importanceClass = match ($link['importance'] ?? null) {
                'high'   => 'importance-high',
                'medium' => 'importance-medium',
                'low'    => 'importance-low',
                default  => '',
            }; ?>

            <a
                class="link-card <?= esc($importanceClass, 'attr') ?>"
                href="<?= esc($link['url'], 'attr') ?>"
                target="_blank"
                rel="noopener noreferrer">

                <div class="flex flex-col items-center justify-center p-4">
                    <span class="font-semibold">
                        <?= esc($link['title']) ?>
                    </span>

                    <span class="text-sm text-base-content/65">
                        <?= esc(display_url($link['url'], 32)) ?>
                    </span>
                </div>

                <div class="flex flex-col gap-1 items-center justify-between p-1">
                    <?php if ((int) ($link['is_favorite'] ?? 0) === 1): ?>
                        <span class="text-red-400" title="Favorite">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                        </span>
                    <?php endif ?>

                    <span class="badge badge-sm font-mono text-base-content/65">
                        <?= esc((string) ($link['click_count'] ?? 0)) ?>
                    </span>

                    <?php if ((int) ($link['is_pending_review'] ?? 0) === 1): ?>
                        <span class="text-orange-400" title="Pending review">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0" /><path d="M12 16h.01" /></svg>
                        </span>
                    <?php endif ?>
                </div>
            </a>

        <?php endforeach ?>
    </div>
</section>
