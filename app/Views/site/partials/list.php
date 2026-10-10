<section class="mt-8">
    <h2 class="text-lg font-bold"><?= esc($collection['name']) ?></h2>

    <div class="mt-4">
        <?php foreach ($collection['links'] as $link): ?>
            <a class="block py-2 hover:underline" href="<?= esc($link['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer">
                <?= esc($link['title']) ?>
            </a>
        <?php endforeach ?>
    </div>
</section>
