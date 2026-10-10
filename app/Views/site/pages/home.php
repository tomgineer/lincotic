<?= $this->extend('site/layouts/default') ?>

<?= $this->section('content') ?>
<main class="mx-auto w-full max-w-5xl px-6 py-16">
    <h1 class="text-4xl font-bold">Lincotic</h1>
    <p class="mt-4 text-lg">
        A clean, lightweight, open-source bookmark manager for organizing and accessing your favorite links.
    </p>

    <?php foreach ($collections as $collection): ?>
        <?= view('site/partials/list', ['collection' => $collection]) ?>
    <?php endforeach ?>
</main>
<?= $this->endSection() ?>
