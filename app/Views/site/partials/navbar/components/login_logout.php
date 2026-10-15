<?php if (auth()->loggedIn()): ?>
    <?php $username = (string) auth()->user()->username; ?>
    <a
        href="<?= esc(site_url('@' . rawurlencode($username))) ?>"
        class="btn btn-lg btn-square btn-ghost text-acid hover:btn-primary hover:text-base-content"
        aria-label="Open your hub"
        title="Open your hub">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
    </a>
    <a
        href="<?= esc(url_to('logout')) ?>"
        class="btn btn-lg btn-square btn-ghost hover:btn-primary"
        aria-label="Sign out"
        title="Sign out">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path d="M10 17l5 -5 -5 -5" /><path d="M15 12H3" /><path d="M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-4" /></svg>
    </a>
<?php else: ?>
    <a
        href="<?= esc(url_to('login')) ?>"
        class="btn btn-lg btn-square <?= url_is('login') ? 'btn-primary' : 'btn-ghost hover:btn-primary' ?>"
        aria-label="Sign in"
        title="Sign in"
        <?= url_is('login') ? 'aria-current="page"' : '' ?>>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path d="M14 8V6a2 2 0 0 0 -2 -2H5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M20 12H7" /><path d="m17 9 3 3-3 3" /></svg>
    </a>
<?php endif ?>
