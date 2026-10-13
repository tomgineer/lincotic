<nav class="sticky top-0">
    <div class="grid grid-cols-[1fr_auto_1fr] h-24 border-b border-base-content/15">

        <div data-component="nav-left">
            <div class="grid grid-cols-[auto_1fr] auto-rows-12">

                <div class="row-span-2 flex items-center px-8 border border-red-400">
                    <a href="<?= esc(site_url('/')) ?>" class="group block" aria-label="Lincotic home">
                        <img
                            src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                            alt="Lincotic"
                            class="h-10 w-auto duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
                    </a>
                </div>

                <div class="flex items-center border border-red-400">
                    <?= $this->include('site/partials/navbar/utilities') ?>
                </div>

                <div class="flex items-center border border-red-400">
                    <ul class="text-base-content/55 uppercase flex flex-col font-ui text-[10px]">
                        <li class="tracking-[0.5em]">Links • Chaotic • Lincotic</li>
                        <li class="tracking-[0.1em]">
                            <span class="text-acid">1,230</span> links collected • <span class="text-acid">234</span> times hammered
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div data-component="nav-center">
            <div class="grid grid-cols-4 auto-rows-12">
                <span class="col-span-4 border border-red-400"></span>

                <div class="flex items-center border border-red-400">
                    test
                </div>
                <div class="flex items-center border border-red-400">
                    test
                </div>
                <div class="flex items-center border border-red-400">
                    test
                </div>
                <div class="flex items-center border border-red-400">
                    test
                </div>
            </div>
        </div>

        <div data-component="nav-right">
            <div class="grid grid-cols-[1fr_auto] auto-rows-12">
                <div class="flex items-center justify-end border border-red-400">
                    <?= $this->include('site/partials/navbar/admin_toolbar') ?>
                </div>
                <div class="row-span-2 flex items-center px-4 border border-red-400">
                    <?= $this->include('site/partials/navbar/login_logout') ?>
                </div>
                <div class="flex items-center justify-end border border-red-400">
                    <?= $this->include('site/partials/navbar/search') ?>
                </div>
            </div>
        </div>

    </div>
</nav>