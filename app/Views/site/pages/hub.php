<?= $this->extend('site/layouts/default') ?>

<?= $this->section('header') ?>
    <?= $this->include('site/partials/navbar/navbar') ?>
<?= $this->endSection() ?>

<?php if ($canEditContent): ?>
    <?= $this->section('vendorScripts') ?>
        <script src="<?= base_url('js/tiptap-dist.js') ?>?v=<?= APP_VERSION ?>" defer></script>
    <?= $this->endSection() ?>
<?php endif ?>

<?= $this->section('content') ?>
<main class="mx-auto max-w-7xl px-6 pt-4 lg:pt-8 pb-16">
    <?= $this->include('site/partials/stats') ?>

    <?php if ($collections === []): ?>
        <div class="rounded-box border border-base-content/10 bg-base-200 px-6 py-12 text-center">
            <h2 class="text-lg font-semibold">No links found</h2>
            <p class="mt-2 text-sm text-base-content/60">Try a different search or filter.</p>
        </div>
    <?php else: ?>
        <?php foreach ($collections as $collection): ?>
            <?= view('site/partials/list', [
                'collection'     => $collection,
                'canEditContent' => $canEditContent,
            ]) ?>
        <?php endforeach ?>
    <?php endif ?>

    <div class="mt-8 flex justify-center md:hidden">
        <a
            href="#top"
            class="btn btn-circle border border-base-content/15 bg-base-50 text-base-content/60 shadow-sm transition-all hover:border-primary/40 hover:bg-primary/10 hover:text-primary active:scale-95"
            aria-label="Back to top"
            title="Back to top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M18 11l-6 -6" /><path d="M6 11l6 -6" /></svg>
        </a>
    </div>

</main>

<?php if ($canEditContent): ?>
    <?= view('site/modals/edit_link', ['collectionOptions' => $collectionOptions]) ?>
    <?= view('site/modals/edit_collection') ?>
    <?= view('site/modals/sort_collections', ['collectionOptions' => $collectionOptions]) ?>
<?php endif ?>

<?php if ($canManageUsers): ?>
    <?= view('site/modals/edit_user') ?>
<?php endif ?>

<?php if ($canEditContent || $canManageUsers): ?>
    <?= view('site/modals/confirmation') ?>
<?php endif ?>

<?= $this->endSection() ?>
