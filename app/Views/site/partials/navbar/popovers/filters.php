<section
    id="navbar-filters-popover"
    class="navbar-popover"
    aria-labelledby="navbar-filters-heading"
    popover="auto">
    <div class="navbar-popover-content">
        <header class="navbar-popover-header">
            <div>
                <h2 id="navbar-filters-heading" class="font-heading text-lg font-semibold">Filters</h2>
                <p class="text-sm text-base-content/55">Narrow down the visible links.</p>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm"
                aria-label="Close filters"
                popovertarget="navbar-filters-popover"
                popovertargetaction="hide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="size-4" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="navbar-popover-body">
            <form action="<?= esc($hubUrl) ?>" method="get" aria-label="Link filters">
                <input type="hidden" name="collection" value="<?= esc($filters['collection'] ?? 'featured', 'attr') ?>">
                <input type="hidden" name="sort" value="<?= esc($filters['sort'] ?? 'importance', 'attr') ?>">

            <div class="grid grid-cols-2 gap-4">
            <fieldset class="rounded-xl border border-base-content/10 bg-base-50 p-5">
                <legend class="sr-only">Filter by flag</legend>

                <div class="flex items-start gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-base-100 text-acid" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 5a5 5 0 0 1 8 0a5 5 0 0 0 8 0v9a5 5 0 0 1 -8 0a5 5 0 0 0 -8 0v7" /></svg>
                    </span>
                    <span>
                        <span class="block font-heading font-semibold">Flag</span>
                        <span class="mt-0.5 block text-sm text-base-content/55">Show links by their review state.</span>
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <?php foreach ([
                        'all'            => ['All flags', 'Every flag state'],
                        'favorite'       => ['Favorites', 'Saved as a favorite'],
                        'pending_review' => ['Pending review', 'Waiting to be reviewed'],
                        'unflagged'       => ['Unflagged', 'No flag assigned'],
                    ] as $value => [$label, $description]): ?>
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-base-content/10 bg-base-100 px-4 py-3 transition-colors hover:border-primary/50 hover:bg-base-200/60">
                            <input
                                type="radio"
                                name="flag"
                                value="<?= esc($value, 'attr') ?>"
                                class="radio radio-primary radio-sm mt-0.5 shrink-0"
                                <?= ($filters['flag'] ?? 'all') === $value ? 'checked' : '' ?>>
                            <span class="min-w-0">
                                <span class="block font-heading text-sm font-semibold"><?= esc($label) ?></span>
                                <span class="mt-0.5 block text-xs leading-5 text-base-content/50"><?= esc($description) ?></span>
                            </span>
                        </label>
                    <?php endforeach ?>
                </div>
            </fieldset>

            <fieldset class="rounded-xl border border-base-content/10 bg-base-50 p-5">
                <legend class="sr-only">Filter by importance</legend>

                <div class="flex items-start gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-base-100 text-acid" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 9v4" /><path d="M12 16v.01" /><path d="M5.07 19h13.86a2 2 0 0 0 1.74 -3l-6.93 -12a2 2 0 0 0 -3.48 0l-6.93 12a2 2 0 0 0 1.74 3" /></svg>
                    </span>
                    <span>
                        <span class="block font-heading font-semibold">Importance</span>
                        <span class="mt-0.5 block text-sm text-base-content/55">Focus on links at a specific priority.</span>
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <?php foreach ([
                        'high'    => ['High', 'Highest-priority links', 'col-start-1 row-start-1'],
                        'medium'  => ['Medium', 'Medium-priority links', 'col-start-1 row-start-2'],
                        'low'     => ['Low', 'Lower-priority links', 'col-start-1 row-start-3'],
                        'all'     => ['All levels', 'Every importance level', 'col-start-2 row-start-1'],
                        'default' => ['Default', 'No importance assigned', 'col-start-2 row-start-2'],
                    ] as $value => [$label, $description, $position]): ?>
                        <label class="<?= $position ?> flex cursor-pointer items-start gap-3 rounded-lg border border-base-content/10 bg-base-100 px-4 py-3 transition-colors hover:border-primary/50 hover:bg-base-200/60">
                            <input
                                type="radio"
                                name="importance"
                                value="<?= esc($value, 'attr') ?>"
                                class="radio radio-primary radio-sm mt-0.5 shrink-0"
                                <?= ($filters['importance'] ?? 'all') === $value ? 'checked' : '' ?>>
                            <span class="min-w-0">
                                <span class="block font-heading text-sm font-semibold"><?= esc($label) ?></span>
                                <span class="mt-0.5 block text-xs leading-5 text-base-content/50"><?= esc($description) ?></span>
                            </span>
                        </label>
                    <?php endforeach ?>
                </div>
            </fieldset>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <a
                    href="<?= esc($hubUrl . '?' . http_build_query([
                        'collection' => $filters['collection'] ?? 'featured',
                        'sort'       => $filters['sort'] ?? 'importance',
                    ]), 'attr') ?>"
                    class="btn btn-ghost min-w-24">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary min-w-32">Apply</button>
            </div>
            </form>
        </div>
    </div>
</section>
