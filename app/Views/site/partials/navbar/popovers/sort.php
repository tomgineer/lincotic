<?php
$sortOptions = [
    'importance' => 'Importance',
    'title'      => 'Title (A–Z)',
    'title_desc' => 'Title (Z–A)',
    'recent'     => 'Most recent',
    'updated'    => 'Recently updated',
    'popular'    => 'Most popular',
    'visited'    => 'Last visited',
    'oldest'     => 'Oldest',
];
$activeSort = $filters['sort'] ?? 'importance';
?>

<section
    id="navbar-sort-popover"
    class="navbar-sort-popover w-64 rounded-box border border-base-content/20 bg-base-100 p-2 shadow-2xl"
    aria-label="Sort links"
    popover="auto">
    <form action="<?= esc(site_url('/')) ?>" method="get">
        <input type="hidden" name="collection" value="<?= esc($filters['collection'] ?? 'featured', 'attr') ?>">
        <input type="hidden" name="flag" value="<?= esc($filters['flag'] ?? 'all', 'attr') ?>">
        <input type="hidden" name="importance" value="<?= esc($filters['importance'] ?? 'all', 'attr') ?>">

        <div class="grid gap-1">
            <?php foreach ($sortOptions as $value => $label): ?>
                <?php $isActive = $activeSort === $value; ?>
                <button
                    type="submit"
                    name="sort"
                    value="<?= esc($value, 'attr') ?>"
                    class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-lg px-3 py-2 text-left transition-colors <?= $isActive ? 'bg-base-200 text-acid' : 'hover:bg-base-200' ?>"
                    <?= $isActive ? 'aria-current="true"' : '' ?>>
                    <span><?= esc($label) ?></span>
                    <?php if ($isActive): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>
                    <?php endif ?>
                </button>
            <?php endforeach ?>
        </div>
    </form>
</section>
