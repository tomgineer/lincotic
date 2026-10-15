<?= $this->extend('site/layouts/default') ?>

<?= $this->section('header') ?>
    <?= $this->include('site/partials/navbar/navbar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto max-w-7xl px-6 pt-4 lg:pt-8 pb-16">
    <?= $this->include('site/partials/slogan') ?>

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
