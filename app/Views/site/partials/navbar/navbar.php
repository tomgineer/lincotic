<nav class="sticky top-0 bg-base-100">
    <div class="grid grid-cols-[1fr_auto_1fr] h-28 py-2 border-b border-base-content/15 relative isolate overflow-hidden">

        <div data-component="nav-left">
            <div class="grid grid-cols-[auto_1fr] auto-rows-12">

                <div class="row-span-2 flex items-center px-8">
                    <a href="<?= esc(site_url('/')) ?>" class="group block" aria-label="Lincotic home">
                        <img
                            src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                            alt="Lincotic"
                            class="h-10 w-auto duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
                    </a>
                </div>

                <div class="flex items-center">
                    <?= $this->include('site/partials/navbar/utilities') ?>
                </div>

                <div class="flex items-center">
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
            <div class="grid grid-cols-1 auto-rows-12">
                <div class="flex items-center">
                    <?= $this->include('site/partials/navbar/filters') ?>
                </div>
                <div class="flex items-center">
                    Middle Bottom
                </div>
            </div>
        </div>

        <div data-component="nav-right">
            <div class="grid grid-cols-[1fr_auto] auto-rows-12">
                <div class="flex items-center justify-end">
                    <?= $this->include('site/partials/navbar/admin_toolbar') ?>
                </div>
                <div class="row-span-2 flex items-center px-4">
                    <?= $this->include('site/partials/navbar/login_logout') ?>
                </div>
                <div class="flex items-center justify-end">
                    <?= $this->include('site/partials/navbar/search') ?>
                </div>
            </div>
        </div>

        <div class="absolute -z-1 -top-[45px] right-8 opacity-10 pointer-events-none">
            <img class="size-[250px]" src="<?= esc(base_url('assets/images/icon.svg')) ?>?v=<?= APP_VERSION ?>" loading="lazy">
        </div>
    </div>
</nav>