<section
    id="navbar-collections-popover"
    class="navbar-popover"
    aria-labelledby="navbar-collections-heading"
    popover="auto">
    <div class="navbar-popover-content">
        <header class="navbar-popover-header">
            <div>
                <h2 id="navbar-collections-heading" class="font-heading text-lg font-semibold">Collections</h2>
                <p class="text-sm text-base-content/55">Browse links by collection.</p>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm"
                aria-label="Close collections"
                popovertarget="navbar-collections-popover"
                popovertargetaction="hide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="size-4" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="navbar-popover-body">
            <?php if ($collectionOptions === []): ?>
                <div class="navbar-popover-placeholder">
                    No collections found.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php foreach ($collectionOptions as $collectionOption): ?>
                        <?php
                        $collectionId = (string) $collectionOption['id'];
                        $isActive = ($filters['collection'] ?? 'featured') === $collectionId;
                        $collectionUrl = site_url('/') . '?' . http_build_query([
                            'collection' => $collectionId,
                            'flag'       => $filters['flag'] ?? 'all',
                            'importance' => $filters['importance'] ?? 'all',
                            'sort'       => $filters['sort'] ?? 'importance',
                        ]);
                        ?>
                        <a
                            href="<?= esc($collectionUrl, 'attr') ?>"
                            class="collection-popover-button"
                            <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <span class="collection-popover-button-icon" aria-hidden="true">
                                <?php if (($collectionOption['icon'] ?? '') !== ''): ?>
                                    <?= $collectionOption['icon'] ?>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 7a3 3 0 0 1 3 -3h3l3 3h6a3 3 0 0 1 3 3v7a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /></svg>
                                <?php endif ?>
                            </span>

                            <span class="collection-popover-button-label">
                                <?= esc($collectionOption['name']) ?>
                            </span>

                            <span class="collection-popover-button-count">
                                <?= esc((string) ($collectionOption['link_count'] ?? 0)) ?>
                            </span>
                        </a>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
