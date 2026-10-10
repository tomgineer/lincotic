<section class="mt-8">
    <h2 class="text-lg font-bold"><?= esc($collection['name']) ?></h2>

    <div class="mt-4 grid grid-cols-[repeat(auto-fill,250px)] gap-4">
        <?php foreach ($collection['links'] as $link): ?>
            <?php $importanceBorder = match ($link['importance'] ?? null) {
                'high'   => 'border-l-4 border-l-rose-400',
                'normal' => 'border-l-4 border-l-orange-300',
                'low'    => 'border-l-4 border-l-blue-400',
                default  => '',
            }; ?>

            <a
                class="p-4 bg-base-200 hover:bg-base-50 hover:text-white
                       flex flex-col gap-1 rounded-xl items-center justify-center
                       <?= esc($importanceBorder, 'attr') ?>"
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
