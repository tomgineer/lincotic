<aside
    id="learn-sidebar"
    class="learn-sidebar fixed inset-y-0 right-0 left-auto m-0 h-dvh max-h-none w-[min(52rem,100vw)] max-w-none overflow-hidden border-0 border-l border-base-content/15 bg-base-100 p-0 text-base-content shadow-[-24px_0_64px_0_rgb(0_0_0_/_0.65)]"
    aria-labelledby="learn-link-title"
    popover="manual"
    data-learn-sidebar
    data-endpoint-template="<?= esc(site_url('learn/links/__LINK_ID__'), 'attr') ?>">
    <div class="flex h-full min-h-0 flex-col">
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-base-content/10 bg-base-200/40 px-8 py-4">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl border border-blue-400/20 bg-blue-400/10 text-blue-400" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6"> <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /> <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /> <path d="M3 6l0 13" /> <path d="M12 6l0 13" /> <path d="M21 6l0 13" /> </svg>
                </span>
                <div>
                    <p class="font-heading text-sm font-semibold text-blue-400">Learn mode</p>
                    <p class="text-xs text-base-content/50">A little context for your next click.</p>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-ghost btn-circle"
                aria-label="Close documentation"
                popovertarget="learn-sidebar"
                popovertargetaction="hide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"> <path d="M18 6l-12 12" /> <path d="M6 6l12 12" /> </svg>
            </button>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-10 py-8" data-learn-content>
            <section aria-labelledby="learn-link-title">
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <span class="badge badge-outline border-blue-400/25 bg-blue-400/5 text-blue-300" data-learn-collection></span>
                    <span class="badge badge-outline gap-1.5 border-yellow-400/25 bg-yellow-400/5 text-yellow-300" data-learn-favorite hidden>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5" aria-hidden="true"> <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" /> </svg>
                        Favorite
                    </span>
                </div>

                <h2 id="learn-link-title" class="font-heading text-4xl leading-tight font-semibold tracking-tight" data-learn-title></h2>
                <p class="mt-3 flex items-center gap-1 font-mono text-sm text-base-content/55">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0" aria-hidden="true"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /> <path d="M3.6 9h16.8" /> <path d="M3.6 15h16.8" /> <path d="M11.5 3a17 17 0 0 0 0 18" /> <path d="M12.5 3a17 17 0 0 1 0 18" /> </svg>
                    <span data-learn-display-url></span>
                </p>
                <p class="mt-6 text-lg leading-relaxed text-base-content/70" data-learn-description hidden></p>

                <a
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn mt-6 gap-2 border-acid/40 bg-acid text-base-100 shadow-none hover:border-acid hover:bg-acid/85"
                    data-learn-open-link>
                    Open link
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"> <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /> <path d="M11 13l9 -9" /> <path d="M15 4h5v5" /> </svg>
                    <span class="sr-only">(opens in a new tab)</span>
                </a>
            </section>

            <section class="mt-9 border-t border-base-content/10 pt-7" aria-labelledby="learn-documentation-title">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <h3 id="learn-documentation-title" class="font-heading text-sm font-semibold tracking-wide text-blue-400">Documentation</h3>
                    <span class="text-xs text-base-content/40">Personal notes</span>
                </div>

                <article class="prose max-w-none" data-learn-documentation hidden></article>

                <p class="hidden rounded-xl border border-dashed border-base-content/15 px-5 py-8 text-center text-sm text-base-content/45" data-learn-documentation-empty>
                    No documentation has been added for this link yet.
                </p>
            </section>

            <section class="mt-9 rounded-2xl border border-base-content/10 bg-base-200/40 p-5" aria-labelledby="learn-link-activity-title">
                <h3 id="learn-link-activity-title" class="mb-4 font-heading text-sm font-semibold">Link activity</h3>
                <dl class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">
                    <div>
                        <dt class="text-xs text-base-content/45">Times opened</dt>
                        <dd class="mt-1 font-mono text-base-content/80" data-learn-click-count></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Last opened</dt>
                        <dd class="mt-1 text-base-content/80"><time data-learn-last-opened></time></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Saved</dt>
                        <dd class="mt-1 text-base-content/80"><time data-learn-created></time></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Notes updated</dt>
                        <dd class="mt-1 text-base-content/80"><time data-learn-documentation-updated></time></dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</aside>
