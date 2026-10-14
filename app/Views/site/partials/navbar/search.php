<form
    action="<?= esc(site_url('search')) ?>"
    class="w-full flex justify-end"
    method="get"
    aria-label="Search links"
    data-link-search-form>

    <div class="input bg-base-200 w-[300px] max-w-full min-w-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 opacity-50" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>

        <input
            type="search"
            name="q"
            class="min-w-0 grow [&::-webkit-search-cancel-button]:hidden"
            placeholder="Search"
            aria-label="Search links"
            value="<?= esc($search, 'attr') ?>"
            autocomplete="off"
            data-link-search
            <?= $search !== '' ? 'autofocus' : '' ?>>

        <button
            type="button"
            class="btn btn-xs btn-square btn-ghost opacity-50"
            aria-label="Clear search"
            title="Clear search"
            data-clear-search
            <?= $search === '' ? 'disabled' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
        </button>
    </div>

</form>
