<form
    action="<?= esc(site_url('search')) ?>"
    method="get"
    aria-label="Search links"
    data-link-search-form>
    <div class="search-control">
        <label class="search-control__field">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 size-5 shrink-0 text-base-content/45" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
            <input
                type="search"
                name="q"
                class="search-control__input"
                placeholder="Search links…"
                aria-label="Search links"
                value="<?= esc($search, 'attr') ?>"
                autocomplete="off"
                data-link-search
                <?= $search !== '' ? 'autofocus' : '' ?>>
        </label>
        <button
            type="button"
            class="search-control__clear"
            aria-label="Clear search"
            title="Clear search"
            data-clear-search
            <?= $search === '' ? 'disabled' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
            </svg>
        </button>
    </div>
</form>
