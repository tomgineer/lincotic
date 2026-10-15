<nav class="site-navbar sticky top-0 z-99 bg-base-100" aria-label="Primary navigation">
    <div class="relative isolate flex h-16 md:gap-2 overflow-hidden border-b border-base-content/15">
        <div class="flex flex-1 md:flex-initial shrink-0 items-center px-4 lg:px-6">
            <a href="<?= esc(site_url('/')) ?>" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-5.5 md:h-7 w-auto">
            </a>
        </div>

        <div class="flex md:flex-1">
            <button
                class="navbar-popover-trigger flex h-full cursor-pointer items-center gap-2 px-4 transition-colors hover:bg-base-200"
                type="button"
                aria-controls="navbar-collections-popover"
                popovertarget="navbar-collections-popover">
                <span>Collections</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <button
                class="navbar-popover-trigger hidden lg:flex h-full cursor-pointer items-center gap-2 px-4 transition-colors hover:bg-base-200"
                type="button"
                aria-controls="navbar-filters-popover"
                popovertarget="navbar-filters-popover">
                <span>Filters</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <button
                class="navbar-sort-popover-trigger navbar-popover-trigger hidden md:flex h-full cursor-pointer items-center gap-2 px-4 transition-colors hover:bg-base-200"
                type="button"
                aria-controls="navbar-sort-popover"
                popovertarget="navbar-sort-popover">
                <span>Sort</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
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

        <div class="hidden lg:flex">
            <?= $this->include('site/partials/navbar/components/search') ?>
        </div>

        <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
            <div class="hidden 2xl:flex 2xl:items-center border-x border-base-content/15 px-4">
                <?= $this->include('site/partials/navbar/components/admin') ?>
            </div>
        <?php endif ?>

        <div class="hidden md:flex md:items-center pr-2">
            <?= $this->include('site/partials/navbar/components/login_logout') ?>
        </div>
    </div>

    <?= $this->include('site/partials/navbar/popovers/collections') ?>
    <?= $this->include('site/partials/navbar/popovers/filters') ?>
    <?= $this->include('site/partials/navbar/popovers/sort') ?>
    <?= $this->include('site/partials/navbar/popovers/utilities') ?>
</nav>
