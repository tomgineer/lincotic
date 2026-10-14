<?php $sortableCollections = $collectionOptions ?? []; ?>

<dialog
    id="sort-collections-modal"
    class="modal"
    aria-labelledby="sort-collections-modal-title"
    aria-describedby="sort-collections-modal-description"
    data-sort-collections-modal
    data-endpoint="<?= esc(site_url('edit/collections/order'), 'attr') ?>"
    data-csrf-header="<?= esc(config('Security')->headerName, 'attr') ?>"
    data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>">
    <div class="modal-box -translate-y-[5vh] flex h-[38rem] max-h-[calc(90dvh-2rem)] w-[calc(100%-2rem)] max-w-2xl flex-col overflow-hidden p-0">
        <header class="flex shrink-0 items-start justify-between gap-6 border-b border-base-content/10 px-6 py-4 sm:px-8">
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-primary/15 text-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M3 6h18" /><path d="M7 12h14" /><path d="M11 18h10" /><path d="m3 15 3 3-3 3" /></svg>
                </span>

                <div class="min-w-0">
                    <h2 id="sort-collections-modal-title" class="font-heading text-xl font-semibold">Sort collections</h2>
                    <p id="sort-collections-modal-description" class="mt-1 text-sm text-base-content/60">
                        Search, set a position, or drag to reorder.
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm shrink-0"
                commandfor="sort-collections-modal"
                command="close"
                aria-label="Close collection sorting">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="shrink-0 px-6 py-3 sm:px-8">
            <?php if ($sortableCollections !== []): ?>
                <div class="mb-3 flex items-center gap-2">
                    <label class="input input-sm flex min-w-0 flex-1 items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 shrink-0 text-base-content/50" aria-hidden="true"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
                        <input type="search" class="min-w-0 grow" placeholder="Find a collection…" aria-label="Find a collection" aria-controls="sort-collections-list" data-sort-collections-search>
                    </label>
                    <button type="button" class="btn btn-ghost btn-sm" data-sort-collections-clear disabled>Clear</button>
                </div>
                <p class="mb-3 text-xs text-base-content/55" data-sort-collections-hint>Enter a position and press Move. Drag or use ↑ / ↓ on a row; Home / End moves it first / last.</p>
            <?php endif ?>
            <div class="flex items-center justify-between gap-4">
                <p class="text-xs text-base-content/55">Changes are applied after saving.</p>
                <span class="badge badge-soft shrink-0 font-mono text-xs">
                    <span data-sort-collections-count aria-live="polite"><?= count($sortableCollections) ?> <?= count($sortableCollections) === 1 ? 'collection' : 'collections' ?></span>
                </span>
            </div>

        </div>

        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-6 pb-5 [scrollbar-gutter:stable] sm:px-8" data-sort-collections-scroll>
            <?php if ($sortableCollections === []): ?>
                <div class="rounded-xl border border-dashed border-base-content/15 bg-base-100 px-5 py-10 text-center">
                    <span class="mx-auto grid size-11 place-items-center rounded-full bg-base-200 text-base-content/45" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M3 7a3 3 0 0 1 3 -3h3l3 3h6a3 3 0 0 1 3 3v7a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /></svg>
                    </span>
                    <p class="mt-3 font-heading text-sm font-semibold">No collections to sort</p>
                    <p class="mt-1 text-xs text-base-content/55">Create at least two collections to arrange their order.</p>
                </div>
            <?php else: ?>
                <ol id="sort-collections-list" class="space-y-2" aria-label="Collection order" data-sort-collections-list>
                    <?php foreach ($sortableCollections as $index => $collection): ?>
                        <li
                            class="group flex cursor-grab items-center gap-2 rounded-xl border border-base-content/10 bg-base-100 px-3 py-2 transition-colors hover:border-primary/50 hover:bg-base-200/60 active:cursor-grabbing active:opacity-70"
                            draggable="true"
                            tabindex="0"
                            data-sort-collection
                            data-collection-name="<?= esc($collection['name'], 'attr') ?>"
                            data-collection-id="<?= esc((string) $collection['id'], 'attr') ?>">
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-base-200 font-mono text-xs text-base-content/55" aria-hidden="true" data-sort-collection-position>
                                <?= $index + 1 ?>
                            </span>

                            <span class="line-clamp-2 min-w-0 flex-1 break-words font-heading text-sm font-semibold sm:block sm:truncate" title="<?= esc($collection['name'], 'attr') ?>">
                                <?= esc($collection['name']) ?>
                            </span>

                            <input
                                type="number" min="1" max="<?= count($sortableCollections) ?>" step="1" value="<?= $index + 1 ?>"
                                class="input input-sm w-16 shrink-0 font-mono text-xs" required
                                aria-label="<?= esc('Move ' . $collection['name'] . ' to position', 'attr') ?>"
                                data-sort-collection-destination>
                            <button type="button" class="btn btn-soft btn-sm shrink-0" data-sort-collection-move aria-label="<?= esc('Move ' . $collection['name'] . ' to entered position', 'attr') ?>">Move</button>

                            <span class="hidden size-6 shrink-0 place-items-center rounded-lg text-base-content/40 sm:grid" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5"><circle cx="9" cy="5" r="1.5" /><circle cx="15" cy="5" r="1.5" /><circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" /><circle cx="9" cy="19" r="1.5" /><circle cx="15" cy="19" r="1.5" /></svg>
                            </span>
                            <span class="sr-only">Drag to reorder <?= esc($collection['name']) ?></span>
                        </li>
                    <?php endforeach ?>
                </ol>
                <p class="py-8 text-center text-sm text-base-content/60" data-sort-collections-empty hidden>No collections match your search.</p>
            <?php endif ?>
        </div>

        <footer class="flex shrink-0 flex-wrap items-center justify-end gap-3 border-t border-base-content/10 bg-base-200/40 px-6 py-3 sm:px-8">
            <p class="min-h-5 w-full break-words text-sm text-base-content/60 empty:hidden sm:w-auto sm:min-w-0 sm:flex-1" aria-live="polite" data-sort-collections-status></p>
            <button
                type="button"
                class="btn btn-ghost"
                commandfor="sort-collections-modal"
                command="close">
                Cancel
            </button>
            <button type="button" class="btn btn-primary min-w-32" data-sort-collections-save <?= count($sortableCollections) < 2 ? 'disabled' : '' ?>>Save order</button>
        </footer>
    </div>

    <div class="modal-backdrop">
        <button
            type="button"
            commandfor="sort-collections-modal"
            command="close"
            aria-label="Close collection sorting">Close</button>
    </div>
</dialog>
