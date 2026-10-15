<section
    id="navbar-utilities-popover"
    class="navbar-popover"
    aria-labelledby="navbar-utilities-heading"
    popover="auto">
    <div class="navbar-popover-content">
        <header class="navbar-popover-header">
            <div>
                <h2 id="navbar-utilities-heading" class="font-heading text-lg font-semibold">Utilities</h2>
                <p class="text-sm text-base-content/55">Open a Lincotic utility.</p>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm"
                aria-label="Close utilities"
                popovertarget="navbar-utilities-popover"
                popovertargetaction="hide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="size-4" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="navbar-popover-body">
            <ul class="divide-y divide-base-content/10 border-y border-base-content/10">
                <?php foreach (config('UtilityCatalog')->items as $utility): ?>
                    <?php $isActive = url_is($utility['active']); ?>
                    <li>
                        <a
                            href="<?= esc(site_url($utility['path'])) ?>"
                            class="group grid cursor-pointer grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-1 px-4 py-4 transition-colors hover:duration-0 sm:grid-cols-[14rem_minmax(0,1fr)_auto] sm:gap-8 <?= $isActive ? 'bg-base-200' : 'hover:bg-base-200' ?>"
                            <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <h3 class="font-medium transition-colors group-hover:duration-0 group-hover:text-acid <?= $isActive ? 'text-acid' : '' ?>">
                                <?= esc($utility['title']) ?>
                            </h3>
                            <p class="col-start-1 row-start-2 font-normal text-base-content/55 sm:col-start-2 sm:row-start-1">
                                <?= esc($utility['description']) ?>
                            </p>
                            <span class="col-start-2 row-span-2 row-start-1 inline-flex items-center gap-1 whitespace-nowrap text-sm font-medium text-base-content/45 transition-colors group-hover:duration-0 group-hover:text-acid sm:col-start-3 sm:row-span-1 <?= $isActive ? 'text-acid' : '' ?>">
                                <span>Open</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 12h.5m3 0h1.5m3 0h6" /><path d="M15 16l4 -4" /><path d="M15 8l4 4" /></svg>
                            </span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</section>
