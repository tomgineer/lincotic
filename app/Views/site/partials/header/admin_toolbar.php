<div class="flex px-2 border-t border-base-content/15 pt-4 pb-4 2xl:pb-0 mt-2 relative">
    <label title="Edit mode">
        <input
            type="checkbox"
            class="peer sr-only"
            aria-label="Edit mode"
            data-edit-mode-toggle>
        <span class="btn btn-lg btn-square btn-ghost font-ui peer-checked:bg-primary peer-checked:text-primary-content peer-checked:border-primary peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg>
        </span>
    </label>

    <button
        class="btn btn-lg btn-square btn-ghost hover:btn-primary"
        title="Add User"
        type="button"
        data-add-user>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
    </button>

    <button class="users-popover-trigger btn btn-lg btn-ghost gap-1 px-3 hover:btn-primary"
        title="Show Users"
        type="button"
        aria-controls="users-popover"
        popovertarget="users-popover">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 7a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <button
        class="btn btn-lg btn-square btn-ghost hover:btn-primary"
        title="Sort Collection"
        type="button"
        data-sort-collections>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 3h3l2 2h5a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2" /><path d="M17 16v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h2" /></svg>
    </button>

    <button
        class="btn btn-lg btn-square btn-ghost hover:btn-primary"
        title="Add Collection"
        type="button"
        data-add-collection>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 19h-7a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v3.5" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
    </button>

    <button
        class="btn btn-lg btn-square btn-ghost hover:btn-primary text-acid hover:text-base-content"
        title="Add Link"
        type="button"
        data-add-link>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.072 0a4.993 4.993 0 0 1 -.001 7.072" /><path d="M12.603 18.534a5.07 5.07 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
    </button>

    <p class="absolute top-0 left-4 -translate-y-1/2 mb-1.5 px-1 bg-base-100 px-2
              font-mono text-[9px] font-bold uppercase tracking-[0.18em] text-acid">
        Admin tools
    </p>

    <?= $this->include('site/partials/header/users_popover') ?>
</div>
