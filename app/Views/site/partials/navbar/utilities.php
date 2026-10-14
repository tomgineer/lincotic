<ul class="flex gap-1">
    <li>
        <a
            href="<?= esc(site_url('password-generator')) ?>"
            class="btn btn-square <?= url_is('password-generator') ? 'btn-primary' : 'btn-ghost hover:btn-primary' ?>"
            aria-label="Password generator"
            title="Password generator"
            <?= url_is('password-generator') ? 'aria-current="page"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2l0 -6" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /><path d="M15 16h.01" /><path d="M12.01 16h.01" /><path d="M9.02 16h.01" /></svg>
        </a>
    </li>
    <li>
        <a
            href="<?= esc(site_url('date-time-calculator')) ?>"
            class="btn btn-square <?= url_is('date-time-calculator') ? 'btn-primary' : 'btn-ghost hover:btn-primary' ?>"
            aria-label="Date and time calculator"
            title="Date and time calculator"
            <?= url_is('date-time-calculator') ? 'aria-current="page"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M12 7v5l3 2" /><circle cx="12" cy="12" r="9" /></svg>
        </a>
    </li>
    <li>
        <a
            href="<?= esc(site_url('hash-generator')) ?>"
            class="btn btn-square <?= url_is('hash-generator*') ? 'btn-primary' : 'btn-ghost hover:btn-primary' ?>"
            aria-label="Hash generator"
            title="Hash generator"
            <?= url_is('hash-generator*') ? 'aria-current="page"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M5 9h14" /><path d="M4 15h14" /><path d="M10 3l-2 18" /><path d="M16 3l-2 18" /></svg>
        </a>
    </li>
    <li>
        <a
            href="<?= esc(site_url('work-time-tracker')) ?>"
            class="btn btn-square <?= url_is('work-time-tracker') ? 'btn-primary' : 'btn-ghost hover:btn-primary' ?>"
            aria-label="Work time tracker"
            title="Work time tracker"
            <?= url_is('work-time-tracker') ? 'aria-current="page"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12v.01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
        </a>
    </li>
</ul>
