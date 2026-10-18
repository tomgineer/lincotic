<button
    type="button"
    class="navbar-settings-popover-trigger btn btn-lg btn-square btn-ghost hover:btn-primary"
    aria-label="Display settings"
    title="Display settings"
    aria-controls="navbar-settings-popover"
    popovertarget="navbar-settings-popover">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 8h4v4h-4l0 -4" /><path d="M6 4l0 4" /><path d="M6 12l0 8" /><path d="M10 14h4v4h-4l0 -4" /><path d="M12 4l0 10" /><path d="M12 18l0 2" /><path d="M16 5h4v4h-4l0 -4" /><path d="M18 4l0 1" /><path d="M18 9l0 11" /></svg>
</button>

<section
    id="navbar-settings-popover"
    class="navbar-settings-popover w-72 rounded-box border border-base-content/20 bg-base-100 p-2 shadow-2xl"
    aria-label="Display settings"
    data-hub-settings
    popover="auto">
    <div class="grid gap-1">
        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg px-3 py-2.5 transition-colors hover:bg-base-200">
            <span class="min-w-0">
                <span class="block font-heading text-sm font-semibold">Full width</span>
                <span id="setting-full-width-description" class="mt-0.5 block text-xs leading-5 text-base-content/50">
                    Use the available screen width and fit more links per row.
                </span>
            </span>
            <input
                type="checkbox"
                class="toggle toggle-primary toggle-sm shrink-0"
                aria-label="Full width"
                aria-describedby="setting-full-width-description"
                data-setting-full-width>
        </label>

        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg px-3 py-2.5 transition-colors hover:bg-base-200">
            <span class="min-w-0">
                <span class="block font-heading text-sm font-semibold">Hide descriptions</span>
                <span id="setting-hide-descriptions-description" class="mt-0.5 block text-xs leading-5 text-base-content/50">
                    Hide collection descriptions for a more compact view.
                </span>
            </span>
            <input
                type="checkbox"
                class="toggle toggle-primary toggle-sm shrink-0"
                aria-label="Hide descriptions"
                aria-describedby="setting-hide-descriptions-description"
                data-setting-hide-descriptions>
        </label>
    </div>
</section>
