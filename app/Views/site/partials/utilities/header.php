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

        <div class="flex items-center px-6 py-6">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-[0.2em] text-acid"><?= esc($eyebrow) ?></p>
                <p class="mt-1 text-sm text-base-content/60"><?= esc($description) ?></p>
            </div>
        </div>

        <div class="flex items-center justify-start px-6 pb-6 xl:px-0 xl:pb-0">
            <?= view('site/partials/utilities/navigation') ?>
        </div>
    </div>
</header>
