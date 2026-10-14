<nav class="lg:sticky lg:top-0 lg:z-99 bg-base-100">
    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] 2xl:grid-cols-[1fr_auto_1fr] h-28
                py-2 border-b border-base-content/15 relative isolate overflow-hidden">

        <div data-component="nav-left">
            <div class="grid grid-cols-1 lg:grid-cols-[auto_1fr] auto-rows-12">
                <div class="lg:row-span-2 flex items-center px-4 lg:px-8 lg:mr-2">
                    <a href="<?= esc(site_url('/')) ?>" class="group block" aria-label="Lincotic home">
                        <img
                            src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                            alt="Lincotic"
                            class="h-8 2xl:h-10 w-auto duration-300 ease-out motion-safe:transition-transform motion-safe:group-hover:scale-105">
                    </a>
                </div>

                <div class="flex items-center">
                    <?= $this->include('site/partials/navbar/utilities') ?>
                </div>

                <div class="hidden lg:flex lg:items-center">
                    <ul class="text-base-content/55 uppercase flex flex-col font-ui text-[10px]">
                        <li class="tracking-[0.5em]">Links • Chaotic • Lincotic</li>
                        <li class="tracking-[0.1em]">
                            <span class="text-acid font-semibold"><?= number_format((int) ($totalLinkCount ?? 0)) ?></span> links collected • <span class="text-acid font-semibold"><?= number_format((int) ($totalClickCount ?? 0)) ?></span> times hammered
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

                </div>
            </div>
        </div>

        <div data-component="nav-right">
            <div class="grid grid-cols-[1fr_auto] auto-rows-12">
                <div class="hidden 2xl:flex 2xl:items-center 2xl:justify-end">
                    <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
                        <?= $this->include('site/partials/navbar/admin_toolbar') ?>
                    <?php endif ?>
                </div>
                <div class="2xl:row-span-2 flex items-center px-4 justify-end">
                    <?= $this->include('site/partials/navbar/login_logout') ?>
                </div>
                <div class="hidden 2xl:flex 2xl:items-center 2xl:justify-end">
                    <?= $this->include('site/partials/navbar/search') ?>
                </div>
            </div>
        </div>

        <div class="absolute -z-1 -top-[45px] right-8 opacity-10 pointer-events-none">
            <img class="size-[250px]" src="<?= esc(base_url('assets/images/icon.svg')) ?>?v=<?= APP_VERSION ?>" loading="lazy">
        </div>
    </div>
</nav>
