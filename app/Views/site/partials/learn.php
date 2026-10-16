<aside
    id="learn-sidebar"
    class="fixed inset-y-0 right-0 left-auto m-0 h-dvh max-h-none w-[min(52rem,100vw)] max-w-none overflow-hidden border-0 border-l border-base-content/15 bg-base-100 p-0 text-base-content shadow-[-24px_0_64px_0_rgb(0_0_0_/_0.65)]"
    aria-labelledby="learn-link-title"
    popover="manual"
    data-learn-sidebar
    data-preview-open>
    <div class="flex h-full min-h-0 flex-col">
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-base-content/10 bg-base-200/40 px-8 py-4">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl border border-blue-400/20 bg-blue-400/10 text-blue-400" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-book size-6">
                        <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                        <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                        <path d="M3 6l0 13" />
                        <path d="M12 6l0 13" />
                        <path d="M21 6l0 13" />
                    </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-x size-6" aria-hidden="true">
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Demo content for designing the sidebar; link data will be connected later. -->
        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-10 py-8" data-learn-content>
            <section aria-labelledby="learn-link-title">
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <span class="badge badge-outline border-blue-400/25 bg-blue-400/5 text-blue-300">Web development</span>
                    <span class="badge badge-outline gap-1.5 border-yellow-400/25 bg-yellow-400/5 text-yellow-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-star size-3.5" aria-hidden="true">
                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" />
                        </svg>
                        Favorite
                    </span>
                </div>

                <h2 id="learn-link-title" class="font-heading text-4xl leading-tight font-semibold tracking-tight">JavaScript Guide</h2>
                <p class="mt-3 flex items-center gap-2 font-mono text-xs text-base-content/45">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-world size-4 shrink-0" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                        <path d="M3.6 9h16.8" />
                        <path d="M3.6 15h16.8" />
                        <path d="M11.5 3a17 17 0 0 0 0 18" />
                        <path d="M12.5 3a17 17 0 0 1 0 18" />
                    </svg>
                    developer.mozilla.org
                </p>
                <p class="mt-6 text-lg leading-relaxed text-base-content/70">A practical reference for understanding JavaScript, from everyday language fundamentals to the patterns you reach for when building real applications.</p>

                <a
                    href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn mt-6 gap-2 border-acid/40 bg-acid text-base-100 shadow-none hover:border-acid hover:bg-acid/85">
                    Open link
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-external-link size-5" aria-hidden="true">
                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                        <path d="M11 13l9 -9" />
                        <path d="M15 4h5v5" />
                    </svg>
                    <span class="sr-only">(opens in a new tab)</span>
                </a>
            </section>

            <section class="mt-9 border-t border-base-content/10 pt-7" aria-labelledby="learn-documentation-title">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <h3 id="learn-documentation-title" class="font-heading text-sm font-semibold tracking-wide text-blue-400">Documentation</h3>
                    <span class="text-xs text-base-content/40">Personal notes</span>
                </div>

                <article class="prose max-w-none">
                    <h2>Why I saved this</h2>
                    <p>This is my starting point when I need to revisit a JavaScript concept. Keep it close while working through a new feature, and turn the useful parts into small, runnable examples.</p>

                    <blockquote>
                        <p>Read a little, try it in the console, then explain it in your own words.</p>
                    </blockquote>

                    <h3>What to focus on</h3>
                    <ul>
                        <li><strong>Scope and closures:</strong> understand which values a function can access.</li>
                        <li><strong>Arrays and objects:</strong> practice transforming data without unexpected mutations.</li>
                        <li><strong>Async code:</strong> get comfortable with promises, <code>async</code> and <code>await</code>.</li>
                    </ul>

                    <h3>A small example</h3>
                    <p>A closure keeps access to its surrounding scope, even after the outer function has returned:</p>
                    <pre><code>function createCounter() {
    let count = 0;

    return () =&gt; ++count;
}

const next = createCounter();
next(); // 1
next(); // 2</code></pre>

                    <h3>Next time I come back</h3>
                    <ol>
                        <li>Recreate the example without looking at the notes.</li>
                        <li>Give the counter a reset method.</li>
                        <li>Find a place in an existing project where this pattern helps.</li>
                    </ol>

                    <hr>
                    <p><em>Keep these notes short enough to revisit. Add examples that helped something click.</em></p>
                </article>
            </section>

            <section class="mt-9 rounded-2xl border border-base-content/10 bg-base-200/40 p-5" aria-labelledby="learn-link-activity-title">
                <h3 id="learn-link-activity-title" class="mb-4 font-heading text-sm font-semibold">Link activity</h3>
                <dl class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">
                    <div>
                        <dt class="text-xs text-base-content/45">Times opened</dt>
                        <dd class="mt-1 font-mono text-base-content/80">24</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Last opened</dt>
                        <dd class="mt-1 text-base-content/80"><time datetime="2026-10-15">15 Oct 2026</time></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Saved</dt>
                        <dd class="mt-1 text-base-content/80"><time datetime="2026-09-28">28 Sep 2026</time></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-base-content/45">Notes updated</dt>
                        <dd class="mt-1 text-base-content/80"><time datetime="2026-10-14">14 Oct 2026</time></dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</aside>
