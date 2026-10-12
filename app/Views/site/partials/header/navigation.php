<nav aria-label="Main navigation" class="flex flex-col gap-2">
    <ul class="flex flex-wrap gap-1">
        <li>
            <a
                href="<?= esc(site_url('password-generator')) ?>"
                class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                aria-label="Password generator"
                title="Password generator">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" /><path d="M19 10v4" /><path d="M17 13l4 -2" /><path d="M17 11l4 2" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('date-time-calculator')) ?>"
                class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                aria-label="Date and time calculator"
                title="Date and time calculator">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M12 7v5l3 2" /><circle cx="12" cy="12" r="9" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('hash-generator')) ?>"
                class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                aria-label="Hash generator"
                title="Hash generator">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M5 9h14" /><path d="M4 15h14" /><path d="M10 3l-2 18" /><path d="M16 3l-2 18" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('work-time-tracker')) ?>"
                class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                aria-label="Work time tracker"
                title="Work time tracker">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12v.01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
            </a>
        </li>
        <li class="ml-1 border-l border-base-content/15 pl-2">
            <?php if (auth()->loggedIn()): ?>
                <a
                    href="<?= esc(url_to('logout')) ?>"
                    class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                    aria-label="Sign out"
                    title="Sign out">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M10 17l5 -5 -5 -5" /><path d="M15 12H3" /><path d="M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-4" /></svg>
                </a>
            <?php else: ?>
                <a
                    href="<?= esc(url_to('login')) ?>"
                    class="btn btn-lg btn-square btn-ghost hover:btn-primary"
                    aria-label="Sign in"
                    title="Sign in">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M14 8V6a2 2 0 0 0 -2 -2H5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M20 12H7" /><path d="m17 9 3 3-3 3" /></svg>
                </a>
            <?php endif ?>
        </li>
    </ul>

    <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
        <div class="flex gap-1">
            <label title="Edit mode">
                <input
                    type="checkbox"
                    class="peer sr-only"
                    aria-label="Edit mode">
                <span class="btn btn-sm btn-ghost font-ui peer-checked:bg-primary peer-checked:text-primary-content peer-checked:border-primary peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg>
                    <span>Edit Mode</span>
                </span>
            </label>
            <button class="btn btn-sm btn-ghost hover:btn-primary" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 19h-7a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v3.5" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
                <span>Add Collection</span>
            </button>

            <button class="btn btn-sm btn-ghost hover:btn-primary" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                <span>Add User</span>
            </button>
        </div>
    <?php endif ?>
</nav>
