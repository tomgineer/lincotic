<?php
    $canEditLinks = auth()->loggedIn() && auth()->user()->inGroup('admin');
    $canManageCollection = $canEditLinks && ! ($collection['is_virtual'] ?? false);
?>

<section class="mt-8">
    <h2 class="text-lg font-bold border-b border-base-content/15 pb-2 mb-4 flex gap-2 items-center">
        <?php if (($collection['icon'] ?? null) !== null): ?>
            <span class="shrink-0 text-primary [&>svg]:block [&>svg]:size-6" aria-hidden="true">
                <?= $collection['icon'] ?>
            </span>
        <?php endif ?>

        <span><?= esc($collection['name']) ?></span>

        <?php if ($canManageCollection): ?>
            <span class="badge badge-sm px-1 font-mono text-secondary">id:<?= esc((string) $collection['id']) ?></span>

            <?php if ((int) ($collection['is_hidden'] ?? 0) === 1): ?>
                <span class="badge badge-sm text-blue-400 px-1" title="Hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-3" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </span>
            <?php endif ?>

            <div class="flex gap-1 ml-1">
                <button
                    class="btn btn-sm btn-soft btn-circle hover:btn-primary"
                    type="button"
                    title="Edit Collection"
                    aria-label="Edit <?= esc($collection['name'], 'attr') ?> collection"
                    data-edit-collection
                    data-collection-id="<?= esc((string) $collection['id'], 'attr') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                </button>
                <button
                    class="btn btn-sm btn-soft btn-circle hover:btn-primary"
                    type="button"
                    title="Add Link"
                    aria-label="Add link to <?= esc($collection['name'], 'attr') ?>"
                    data-add-link
                    data-collection-id="<?= esc((string) $collection['id'], 'attr') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.072 0a4.993 4.993 0 0 1 -.001 7.072" /><path d="M12.603 18.534a5.07 5.07 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
                </button>
            </div>
        <?php endif ?>

    </h2>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
        <?php if ($collection['links'] === []): ?>
            <p class="col-span-full rounded-xl border border-dashed border-base-content/15 px-5 py-8 text-center text-sm text-base-content/55">
                This collection does not have any links yet.
            </p>
        <?php endif ?>

        <?php foreach ($collection['links'] as $link): ?>
            <?php $importanceClass = match ($link['importance'] ?? null) {
                'high'   => 'importance-high',
                'medium' => 'importance-medium',
                'low'    => 'importance-low',
                default  => '',
            }; ?>

            <a
                class="link-card <?= esc($importanceClass, 'attr') ?>"
                href="<?= site_url('go/' . $link['id']) ?>"
                <?php if ($canEditLinks): ?>
                    data-link-id="<?= esc((string) $link['id'], 'attr') ?>"
                <?php endif ?>>

                <div class="flex flex-col items-center justify-center p-4 text-center">
                    <span class="font-semibold leading-snug">
                        <?= esc($link['title']) ?>
                    </span>

                    <span class="text-sm text-base-content/65">
                        <?= esc($link['display_url']) ?>
                    </span>
                </div>

                <div class="flex flex-col gap-1 items-center justify-between p-1">

                    <span class="badge badge-sm font-mono text-base-content/65">
                        <?= esc((string) ($link['click_count'] ?? 0)) ?>
                    </span>

                    <?php if ((int) ($link['is_favorite'] ?? 0) === 1): ?>
                        <span class="text-red-400" title="Favorite">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                        </span>
                    <?php endif ?>

                    <?php if ((int) ($link['is_hidden'] ?? 0) === 1): ?>
                        <span class="text-blue-400" title="Hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </span>
                    <?php endif ?>

                    <?php if ((int) ($link['is_pending_review'] ?? 0) === 1): ?>
                        <span class="text-orange-400" title="Pending review">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0" /><path d="M12 16h.01" /></svg>
                        </span>
                    <?php endif ?>
                </div>
            </a>

        <?php endforeach ?>
    </div>

    <div class="mt-8 flex justify-center md:hidden">
        <a
            href="#<?= esc(body_id(), 'attr') ?>"
            class="btn btn-circle border border-base-content/15 bg-base-50 text-base-content/60 shadow-sm transition-all hover:border-primary/40 hover:bg-primary/10 hover:text-primary active:scale-95"
            aria-label="Back to top"
            title="Back to top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M18 11l-6 -6" /><path d="M6 11l6 -6" /></svg>
        </a>
    </div>
</section>
