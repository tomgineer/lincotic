<nav class="navbar-new sticky top-0 z-99 bg-base-100">
    <div class="h-16 border-b border-base-content/15 relative isolate overflow-hidden
                grid grid-cols-[auto_1fr_auto_auto_auto]">

        <div class="flex items-center px-4 lg:px-8 border border-red-400" data-component="logo">
            <a href="<?= esc(site_url('/')) ?>" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-7 w-auto">
            </a>
        </div>

        <div class="flex border border-red-400" data-component="main">
            <button
                class="navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer
                    hover:bg-base-200 transition-colors"
                type="button"
                aria-controls="navbar-collections-popover"
                popovertarget="navbar-collections-popover">
                <span>Collections</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <button
                class="navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer
                    hover:bg-base-200 transition-colors"
                type="button"
                aria-controls="navbar-filters-popover"
                popovertarget="navbar-filters-popover">
                <span>Filters</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <button
                class="navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer
                    hover:bg-base-200 transition-colors"
                type="button"
                aria-controls="navbar-sort-popover"
                popovertarget="navbar-sort-popover">
                <span>Sort</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </div>

        <div class="border border-red-400" data-component="utilities">
            <button
                class="navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer
                    hover:bg-base-200 transition-colors"
                type="button"
                aria-controls="navbar-utilities-popover"
                popovertarget="navbar-utilities-popover">
                <span>Utilities</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </div>

        <div class="flex items-center border border-red-400" data-component="admin">
            Admin
        </div>

        <div class="flex items-center px-2 border border-red-400" data-component="login">
            <?= $this->include('site/partials/navbar/login_logout') ?>
        </div>

    </div>

    <?= $this->include('site/partials/navbar/popovers/collections') ?>
    <?= $this->include('site/partials/navbar/popovers/filters') ?>
    <?= $this->include('site/partials/navbar/popovers/sort') ?>
    <?= $this->include('site/partials/navbar/popovers/utilities') ?>

</nav>
