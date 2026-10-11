<?= $this->extend('site/layouts/default') ?>

<?= $this->section('header') ?>
<?= view('site/partials/header', [
    'collectionOptions' => $collectionOptions,
    'filters'           => $filters,
]) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto max-w-5xl px-6 py-16">
    <?php if ($collections === []): ?>
        <div class="rounded-box border border-base-content/10 bg-base-200 px-6 py-12 text-center">
            <h2 class="text-lg font-semibold">No links found</h2>
            <p class="mt-2 text-sm text-base-content/60">Try a different collection, flag, or sort order.</p>
        </div>
    <?php else: ?>
        <?php foreach ($collections as $collection): ?>
            <?= view('site/partials/list', ['collection' => $collection]) ?>
        <?php endforeach ?>
    <?php endif ?>
</main>

<?= $this->endSection() ?>
