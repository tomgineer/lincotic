<?php

namespace App\Services;

use App\Libraries\UrlFormatter;
use App\Models\CollectionModel;
use App\Models\LinkModel;

final class LibraryQueryService {

    private const int DISPLAY_URL_MAX_LENGTH = 32;

    private const array SORTS = [
        'title'      => ['title', 'ASC'],
        'title_desc' => ['title', 'DESC'],
        'recent'     => ['created_at', 'DESC'],
        'updated'    => ['updated_at', 'DESC'],
        'popular'    => ['click_count', 'DESC'],
        'visited'    => ['last_clicked_at', 'DESC'],
        'oldest'     => ['created_at', 'ASC'],
    ];

    public function __construct(
        private readonly UrlFormatter $urlFormatter,
    ) {}

    /**
     * Returns the size of the complete library, regardless of visibility.
     */
    public function countAllLinks(): int {
        return (new LinkModel())->countAllResults();
    }

    /**
     * Returns the accumulated visits across the complete library.
     */
    public function countAllClicks(): int {
        $result = (new LinkModel())
            ->selectSum('click_count', 'total_clicks')
            ->first();

        return (int) ($result['total_clicks'] ?? 0);
    }

    /**
     * Returns the collections available in the collection filter.
     */
    public function getFilterOptions(bool $includeHidden = false): array {
        $collectionModel = new CollectionModel();

        $this->applyCollectionVisibilityFilter($collectionModel, $includeHidden);

        $collections = $collectionModel
            ->select('id, name, icon')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        if ($collections === []) {
            return [];
        }

        $linkModel = (new LinkModel())->whereIn('collection_id', array_column($collections, 'id'));

        $this->applyVisibilityFilter($linkModel, $includeHidden);

        $counts = $linkModel
            ->select('collection_id, COUNT(id) AS link_count', false)
            ->groupBy('collection_id')
            ->findAll();
        $countsByCollection = array_column($counts, 'link_count', 'collection_id');

        return array_map(
            static fn(array $collection): array => [
                ...$collection,
                'link_count' => (int) ($countsByCollection[$collection['id']] ?? 0),
            ],
            $collections,
        );
    }

    /**
     * Retrieves collections and links matching the selected home filters.
     */
    public function filter(array $filters, bool $includeHidden = false): array {
        if ($filters['unassignedOnly'] ?? false) {
            return $this->findUnassignedLinks($filters, $includeHidden);
        }

        $collections = $this->findCollections($filters, $includeHidden);

        if ($collections === []) {
            return [];
        }

        $links = $this->findFilteredLinks($collections, $filters, $includeHidden);

        return $this->attachLinks(
            $collections,
            $links,
            $this->shouldIncludeEmptyCollections($filters),
        );
    }

    /**
     * Returns links that are no longer assigned to a collection as a virtual group.
     */
    private function findUnassignedLinks(array $filters, bool $includeHidden): array {
        $linkModel = (new LinkModel())->where('collection_id', null);

        $this->applyVisibilityFilter($linkModel, $includeHidden);
        $this->applyFlagFilter($linkModel, $filters['flag'] ?? 'all');
        $this->applyImportanceFilter($linkModel, $filters['importance'] ?? 'all');
        $this->applySort($linkModel, $filters['sort'] ?? 'importance');

        $links = $linkModel->findAll();

        if ($links === []) {
            return [];
        }

        return [[
            'id'         => null,
            'name'       => 'Unassigned Links',
            'is_virtual' => true,
            'links'      => $this->prepareLinksForDisplay($links),
        ]];
    }

    /**
     * Searches all links without applying any of the home filters.
     */
    public function search(string $term, bool $includeHidden = false): array {
        $collections = $this->findCollections([], $includeHidden);

        if ($collections === []) {
            return [];
        }

        $linkModel = $this->linksIn($collections);

        $this->applyVisibilityFilter($linkModel, $includeHidden);
        $linkModel
            ->groupStart()
            ->like('title', $term)
            ->orLike('url', $term)
            ->orLike('description', $term)
            ->groupEnd()
            ->orderBy('title', 'ASC');

        return $this->attachLinks($collections, $linkModel->findAll());
    }

    /**
     * Finds collections that match the selected collection scope.
     */
    private function findCollections(array $filters = [], bool $includeHidden = false): array {
        $collectionModel = new CollectionModel();

        $this->applyCollectionVisibilityFilter($collectionModel, $includeHidden);

        if ($filters['featuredOnly'] ?? false) {
            $collectionModel->where('is_featured', 1);
        }

        if (($filters['collectionId'] ?? null) !== null) {
            $collectionModel->where('id', $filters['collectionId']);
        }

        return $collectionModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Hidden collections are available to authenticated users only.
     */
    private function applyCollectionVisibilityFilter(
        CollectionModel $collectionModel,
        bool $includeHidden,
    ): void {
        if (! $includeHidden) {
            $collectionModel->where('is_hidden', 0);
        }
    }

    /**
     * Finds links within the selected collections and applies all link filters.
     */
    private function findFilteredLinks(array $collections, array $filters, bool $includeHidden): array {
        $linkModel = $this->linksIn($collections);

        $this->applyVisibilityFilter($linkModel, $includeHidden);
        $this->applyFlagFilter($linkModel, $filters['flag'] ?? 'all');
        $this->applyImportanceFilter($linkModel, $filters['importance'] ?? 'all');
        $this->applySort($linkModel, $filters['sort'] ?? 'importance');

        return $linkModel->findAll();
    }

    private function linksIn(array $collections): LinkModel {
        return (new LinkModel())->whereIn(
            'collection_id',
            array_column($collections, 'id')
        );
    }

    /**
     * Hidden links are available to authenticated users only.
     */
    private function applyVisibilityFilter(LinkModel $linkModel, bool $includeHidden): void {
        if (! $includeHidden) {
            $linkModel->where('is_hidden', 0);
        }
    }

    /**
     * Applies the selected favorite or review-state filter to the link query.
     */
    private function applyFlagFilter(LinkModel $linkModel, string $flag): void {
        match ($flag) {
            'favorite'       => $linkModel->where('is_favorite', 1),
            'pending_review' => $linkModel->where('is_pending_review', 1),
            'unflagged'      => $linkModel
                ->where('is_favorite', 0)
                ->where('is_pending_review', 0),
            default          => null,
        };
    }

    /**
     * Applies the selected importance level, including the default NULL value.
     */
    private function applyImportanceFilter(LinkModel $linkModel, string $importance): void {
        if (in_array($importance, ['high', 'medium', 'low'], true)) {
            $linkModel->where('importance', $importance);
        } elseif ($importance === 'default') {
            $linkModel->where('importance', null);
        }
    }

    /**
     * Applies the requested ordering with a title-based tie-breaker.
     */
    private function applySort(LinkModel $linkModel, string $sort): void {
        if ($sort === 'importance' || ! isset(self::SORTS[$sort])) {
            $linkModel
                ->orderBy(
                    "CASE importance WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END",
                    'ASC',
                    false,
                )
                ->orderBy('title', 'ASC');

            return;
        }

        [$sortField, $sortDirection] = self::SORTS[$sort];

        $linkModel->orderBy($sortField, $sortDirection);

        if ($sortField !== 'title') {
            $linkModel->orderBy('title', 'ASC');
        }
    }

    /**
     * Groups links by collection and removes collections with no matching links.
     */
    private function attachLinks(
        array $collections,
        array $links,
        bool $includeEmpty = false,
    ): array {
        $linksByCollection = [];

        foreach ($this->prepareLinksForDisplay($links) as $link) {
            $linksByCollection[$link['collection_id']][] = $link;
        }

        $collectionsWithLinks = array_map(
            static fn(array $collection): array => [
                ...$collection,
                'links' => $linksByCollection[$collection['id']] ?? [],
            ],
            $collections
        );

        if ($includeEmpty) {
            return $collectionsWithLinks;
        }

        return array_values(array_filter(
            $collectionsWithLinks,
            static fn(array $collection): bool => $collection['links'] !== [],
        ));
    }

    /**
     * Adds the compact URL label consumed by the link-card view.
     */
    private function prepareLinksForDisplay(array $links): array {
        return array_map(
            fn(array $link): array => [
                ...$link,
                'display_url' => $this->urlFormatter->forDisplay(
                    (string) ($link['url'] ?? ''),
                    self::DISPLAY_URL_MAX_LENGTH,
                ),
            ],
            $links,
        );
    }

    /**
     * Keeps genuinely empty collections manageable without filling narrowed
     * filter results with collections that simply have no matching links.
     */
    private function shouldIncludeEmptyCollections(array $filters): bool {
        return ($filters['collectionId'] ?? null) !== null
            || (
                ($filters['flag'] ?? 'all') === 'all'
                && ($filters['importance'] ?? 'all') === 'all'
            );
    }
}
