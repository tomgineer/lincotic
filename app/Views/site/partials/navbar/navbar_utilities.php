<nav class="site-navbar sticky top-0 z-99 bg-base-100" aria-label="Utility navigation">
    <div class="h-16 lg:h-20 border-b border-base-content/15">
        <div class="relative isolate mx-auto flex h-full w-full max-w-7xl overflow-hidden md:gap-2">
            <div class="flex flex-1 shrink-0 items-center px-4 md:flex-initial lg:px-6">
                <a href="<?= esc(site_url('/')) ?>" aria-label="Lincotic home">
                    <img
                        src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                        alt="Lincotic"
                        class="h-5.5 md:h-7 lg:h-8 w-auto">
                </a>
            </div>

            <div class="hidden min-w-0 items-center px-4 md:flex md:flex-1">
                <div class="min-w-0">
                    <p class="truncate font-mono text-xs font-bold uppercase tracking-[0.2em] text-acid">
                        <?= esc($eyebrow) ?>
                    </p>
                    <p class="mt-1 truncate text-sm text-base-content/60">
                        <?= esc($description) ?>
                    </p>
                </div>
            </div>

            <div class="flex">
                <button
                    class="navbar-popover-trigger flex h-full cursor-pointer items-center gap-2 px-4 transition-colors hover:bg-base-200"
                    type="button"
                    aria-controls="navbar-utilities-popover"
                    popovertarget="navbar-utilities-popover">
                    <span>Utilities</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </div>

            <div class="hidden pr-2 md:flex md:items-center">
                <?= $this->include('site/partials/navbar/components/login_logout') ?>
            </div>
        </div>
    </div>

    <?= $this->include('site/partials/navbar/popovers/utilities') ?>
</nav>
