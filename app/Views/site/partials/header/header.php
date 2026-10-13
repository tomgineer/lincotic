<header id="site-header" class="library-header z-20 border-b border-base-content/15 bg-base-100 md:sticky md:top-0 relative">
    <span class="library-header__decoration" aria-hidden="true"></span>

    <div class="grid grid-cols-1 2xl:grid-cols-[1fr_min(100%,64rem)_1fr]">
        <div class="flex items-center px-6 pt-6 2xl:justify-end 2xl:py-6">
            <a href="<?= esc(site_url('/')) ?>" class="group block w-full max-w-42" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-auto w-full duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
            </a>
        </div>

        <div class="order-3 grid grid-cols-1 gap-3 px-6 pt-3 pb-6 md:order-2 md:py-6">
            <?= view('site/partials/header/filters', [
                'collectionOptions' => $collectionOptions,
                'filters'           => $filters,
            ]) ?>
            <?= view('site/partials/header/search', ['search' => $search]) ?>
        </div>

        <div class="order-2 flex flex-col items-start justify-center gap-2 pt-3 md:order-3 md:flex-row md:items-center md:justify-between md:pt-0 2xl:flex-col 2xl:items-start 2xl:justify-center">
            <?= $this->include('site/partials/navigation') ?>

            <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
                <?= $this->include('site/partials/header/admin_toolbar') ?>
            <?php endif ?>
        </div>

    </div>

    <div class="hidden md:flex md:gap-1 absolute right-0 top-full mt-1 text-[10px] font-mono text-base-content/75">
        <div class="px-3 py-2 bg-base-200">
            <span class="font-semibold"><?= number_format((int) ($totalLinkCount ?? 0)) ?></span> Total Links
        </div>

        <div class="px-3 py-2 bg-base-200">
            <span class="font-semibold"><?= number_format((int) ($totalClickCount ?? 0)) ?></span> Total Clicks
        </div>
    </div>


</header>
