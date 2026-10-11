<header id="site-header" class="sticky top-0 z-20 border-b border-base-content/15 bg-base-100">
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_min(100%,64rem)_1fr]">
        <div class="flex items-center px-6 pt-6 xl:justify-end xl:py-6">
            <a href="<?= esc(site_url('/')) ?>" class="block w-full max-w-42" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-auto w-full">
            </a>
        </div>

        <form
            action="<?= esc(site_url('/')) ?>"
            method="get"
            class="grid grid-cols-1 gap-3 px-6 py-6 sm:grid-cols-2 lg:grid-cols-4"
            aria-label="Link filters"
            data-link-filters>
            <fieldset class="fieldset min-w-0">
                <legend class="fieldset-legend font-heading">Collections</legend>
                <select name="collection" class="collection-select select w-full bg-base-50" aria-label="Filter links by collection">
                    <optgroup>
                        <legend>Views</legend>
                        <option value="all" <?= $filters['collection'] === 'all' ? 'selected' : '' ?>>All Collections</option>
                        <option value="featured" <?= $filters['collection'] === 'featured' ? 'selected' : '' ?>>Featured Collections</option>
                    </optgroup>
                    <optgroup>
                        <legend>Collections</legend>
                        <?php foreach ($collectionOptions as $collectionOption): ?>
                            <option
                                value="<?= esc((string) $collectionOption['id'], 'attr') ?>"
                                <?= $filters['collection'] === (string) $collectionOption['id'] ? 'selected' : '' ?>>
                                <?= esc($collectionOption['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </optgroup>
                </select>
            </fieldset>

            <fieldset class="fieldset min-w-0">
                <legend class="fieldset-legend font-heading">Flag</legend>
                <select name="flag" class="select w-full bg-base-50" aria-label="Filter links by flag">
                    <option value="all" <?= $filters['flag'] === 'all' ? 'selected' : '' ?>>All Flags</option>
                    <option value="favorite" <?= $filters['flag'] === 'favorite' ? 'selected' : '' ?>>Favorites</option>
                    <option value="pending_review" <?= $filters['flag'] === 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="unflagged" <?= $filters['flag'] === 'unflagged' ? 'selected' : '' ?>>Unflagged</option>
                </select>
            </fieldset>

            <fieldset class="fieldset min-w-0">
                <legend class="fieldset-legend font-heading">Importance</legend>
                <select name="importance" class="select w-full bg-base-50" aria-label="Filter links by importance">
                    <option value="all" <?= $filters['importance'] === 'all' ? 'selected' : '' ?>>All Levels</option>
                    <option value="high" <?= $filters['importance'] === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= $filters['importance'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low" <?= $filters['importance'] === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="default" <?= $filters['importance'] === 'default' ? 'selected' : '' ?>>Default</option>
                </select>
            </fieldset>

            <fieldset class="fieldset min-w-0">
                <legend class="fieldset-legend font-heading">Sort Order</legend>
                <select name="sort" class="select w-full bg-base-50" aria-label="Sort links">
                    <option value="title" <?= $filters['sort'] === 'title' ? 'selected' : '' ?>>Title (A–Z)</option>
                    <option value="title_desc" <?= $filters['sort'] === 'title_desc' ? 'selected' : '' ?>>Title (Z–A)</option>
                    <option value="recent" <?= $filters['sort'] === 'recent' ? 'selected' : '' ?>>Most Recent</option>
                    <option value="updated" <?= $filters['sort'] === 'updated' ? 'selected' : '' ?>>Recently Updated</option>
                    <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Most Visited</option>
                    <option value="visited" <?= $filters['sort'] === 'visited' ? 'selected' : '' ?>>Last Visited</option>
                    <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Oldest</option>
                </select>
            </fieldset>

            <fieldset class="fieldset min-w-0 sm:col-span-2 lg:col-span-4">
                <input
                    type="search"
                    name="q"
                    class="input input-lg w-full bg-base-50"
                    placeholder="Search links…"
                    aria-label="Search links"
                    value="<?= esc($filters['search'], 'attr') ?>"
                    autocomplete="off"
                    <?= $filters['search'] !== '' ? 'autofocus' : '' ?>>
            </fieldset>
        </form>

        <div aria-hidden="true"></div>
    </div>
</header>
