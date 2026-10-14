<nav
    id="site-header"
    class="sticky top-0 z-20 border-b border-base-content/15 bg-base-100"
    aria-label="Utility navigation">
    <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-y-4 px-6 py-5 md:grid-cols-[auto_minmax(0,1fr)] md:items-center md:gap-x-6 lg:grid-cols-[auto_minmax(0,1fr)_auto] lg:gap-x-8 lg:py-6">
        <div class="flex items-center">
            <a href="<?= esc(site_url('/')) ?>" class="group block w-full max-w-42" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-auto w-full duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
            </a>
        </div>

        <div class="flex min-w-0 items-center">
            <div class="min-w-0">
                <p class="font-mono text-xs font-bold uppercase tracking-[0.2em] text-acid"><?= esc($eyebrow) ?></p>
                <p class="mt-1 text-sm text-base-content/60"><?= esc($description) ?></p>
            </div>
        </div>

        <div class="flex items-center justify-start gap-1 md:col-span-2 md:justify-end lg:col-span-1">
            <?= $this->include('site/partials/navbar/utilities') ?>
            <?= $this->include('site/partials/navbar/login_logout') ?>
        </div>
    </div>
</nav>
