<header class="sticky top-0 z-20 border-b border-base-content/10 bg-base-200/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-6 py-6">
        <form
            action="<?= esc(site_url('/')) ?>"
            method="get"
            class="flex flex-wrap items-end gap-3"
            aria-label="Link filters"
            data-link-filters>

            <fieldset class="fieldset min-w-56 flex-1">
                <legend class="fieldset-legend font-heading">Search</legend>
                <input
                    type="search"
                    class="input w-full"
                    placeholder="Search links…"
                    aria-label="Search links"
                    autocomplete="off">
            </fieldset>

            <fieldset class="fieldset w-44">
                <legend class="fieldset-legend font-heading">Collections</legend>
                <select name="collection" class="collection-select select w-full" aria-label="Filter links by collection">
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

            <fieldset class="fieldset w-40">
                <legend class="fieldset-legend font-heading">Flag</legend>
                <select name="flag" class="select w-full" aria-label="Filter links by flag">
                    <option value="all" <?= $filters['flag'] === 'all' ? 'selected' : '' ?>>All Flags</option>
                    <option value="favorite" <?= $filters['flag'] === 'favorite' ? 'selected' : '' ?>>Favorites</option>
                    <option value="pending_review" <?= $filters['flag'] === 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="unflagged" <?= $filters['flag'] === 'unflagged' ? 'selected' : '' ?>>Unflagged</option>
                </select>
            </fieldset>

            <fieldset class="fieldset w-40">
                <legend class="fieldset-legend font-heading">Importance</legend>
                <select name="importance" class="select w-full" aria-label="Filter links by importance">
                    <option value="all" <?= $filters['importance'] === 'all' ? 'selected' : '' ?>>All Levels</option>
                    <option value="high" <?= $filters['importance'] === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="normal" <?= $filters['importance'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="low" <?= $filters['importance'] === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="none" <?= $filters['importance'] === 'none' ? 'selected' : '' ?>>No Importance</option>
                </select>
            </fieldset>

            <fieldset class="fieldset w-48">
                <legend class="fieldset-legend font-heading">Sort Order</legend>
                <select name="sort" class="select w-full" aria-label="Sort links">
                    <option value="title" <?= $filters['sort'] === 'title' ? 'selected' : '' ?>>Title (A–Z)</option>
                    <option value="title_desc" <?= $filters['sort'] === 'title_desc' ? 'selected' : '' ?>>Title (Z–A)</option>
                    <option value="recent" <?= $filters['sort'] === 'recent' ? 'selected' : '' ?>>Most Recent</option>
                    <option value="updated" <?= $filters['sort'] === 'updated' ? 'selected' : '' ?>>Recently Updated</option>
                    <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Most Visited</option>
                    <option value="visited" <?= $filters['sort'] === 'visited' ? 'selected' : '' ?>>Last Visited</option>
                    <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Oldest</option>
                </select>
            </fieldset>
        </form>
    </div>
</header>
