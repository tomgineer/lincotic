<nav aria-label="Utility navigation">
    <ul class="flex flex-wrap gap-1">
        <li>
            <a
                href="<?= esc(site_url('password-generator')) ?>"
                class="btn btn-lg btn-square <?= url_is('password-generator') ? 'btn-primary' : 'btn-ghost' ?>"
                aria-label="Password generator"
                title="Password generator"
                <?= url_is('password-generator') ? 'aria-current="page"' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" /><path d="M19 10v4" /><path d="M17 13l4 -2" /><path d="M17 11l4 2" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('date-time-calculator')) ?>"
                class="btn btn-lg btn-square <?= url_is('date-time-calculator') ? 'btn-primary' : 'btn-ghost' ?>"
                aria-label="Date and time calculator"
                title="Date and time calculator"
                <?= url_is('date-time-calculator') ? 'aria-current="page"' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M12 7v5l3 2" /><circle cx="12" cy="12" r="9" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('hash-generator')) ?>"
                class="btn btn-lg btn-square <?= url_is('hash-generator*') ? 'btn-primary' : 'btn-ghost' ?>"
                aria-label="Hash generator"
                title="Hash generator"
                <?= url_is('hash-generator*') ? 'aria-current="page"' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M5 9h14" /><path d="M4 15h14" /><path d="M10 3l-2 18" /><path d="M16 3l-2 18" /></svg>
            </a>
        </li>
        <li>
            <a
                href="<?= esc(site_url('work-time-tracker')) ?>"
                class="btn btn-lg btn-square <?= url_is('work-time-tracker') ? 'btn-primary' : 'btn-ghost' ?>"
                aria-label="Work time tracker"
                title="Work time tracker"
                <?= url_is('work-time-tracker') ? 'aria-current="page"' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12v.01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
            </a>
        </li>
    </ul>
</nav>
