<section class="mt-8">
    <h2 class="text-lg font-bold border-b border-base-content/15 pb-2 mb-4">
        <?= esc($collection['name']) ?>
    </h2>

    <div class="mt-4 grid grid-cols-3 gap-4">
        <?php foreach ($collection['links'] as $link): ?>
            <?php $importanceClass = match ($link['importance'] ?? null) {
                'high'   => 'importance-high',
                'normal' => 'importance-normal',
                'low'    => 'importance-low',
                default  => '',
            }; ?>

            <a
                class="link-card
                       <?= esc($importanceClass, 'attr') ?>"
                href="<?= esc($link['url'], 'attr') ?>"
                target="_blank"
                rel="noopener noreferrer">

                <span class="font-semibold">
                    <?= esc($link['title']) ?>
                </span>

                <span class="text-sm text-base-content/65">
                    <?= esc(display_url($link['url'], 32)) ?>
                </span>
            </a>

        <?php endforeach ?>
    </div>
</section>
