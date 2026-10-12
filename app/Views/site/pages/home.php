<?= $this->extend('site/layouts/default') ?>

<?= $this->section('header') ?>
<header id="site-header" class="sticky top-0 z-20 border-b border-base-content/15 bg-base-100">
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_min(100%,64rem)_1fr]">
        <div class="flex items-center px-6 pt-6 xl:justify-end xl:py-6">
            <a href="<?= esc(site_url('/')) ?>" class="group block w-full max-w-42" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-auto w-full duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
            </a>
        </div>

        <div class="grid grid-cols-1 gap-3 px-6 py-6">
            <?= view('site/partials/header/filters', [
                'collectionOptions' => $collectionOptions,
                'filters'           => $filters,
            ]) ?>
            <?= view('site/partials/header/search', ['search' => $search]) ?>
        </div>

        <div class="flex items-center justify-start">
            <?= view('site/partials/header/navigation') ?>
        </div>
    </div>
</header>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto max-w-5xl px-6 pt-10 pb-16">
    <?php if ($collections === []): ?>
        <div class="rounded-box border border-base-content/10 bg-base-200 px-6 py-12 text-center">
            <h2 class="text-lg font-semibold">No links found</h2>
            <p class="mt-2 text-sm text-base-content/60">Try a different search or filter.</p>
        </div>
    <?php else: ?>
        <?php foreach ($collections as $collection): ?>
            <?= view('site/partials/list', ['collection' => $collection]) ?>
        <?php endforeach ?>
    <?php endif ?>
</main>

<?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
    <?= view('site/modals/edit_link', ['collectionOptions' => $collectionOptions]) ?>
    <?= view('site/modals/edit_collection') ?>
    <?= view('site/modals/edit_user') ?>
    <?= view('site/modals/sort_collections', ['collectionOptions' => $collectionOptions]) ?>
    <?= view('site/modals/confirmation') ?>
<?php endif ?>

<?= $this->endSection() ?>
