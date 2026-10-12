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

        <div class="flex flex-col items-start justify-center gap-2">
            <?= $this->include('site/partials/navigation') ?>

            <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
                <?= $this->include('site/partials/header/admin_toolbar') ?>
            <?php endif ?>
        </div>

    </div>

</header>
