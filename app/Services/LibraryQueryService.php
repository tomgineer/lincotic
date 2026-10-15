<?php

namespace App\Services;

use App\Libraries\UrlFormatter;
use App\Models\CollectionModel;
use App\Models\LinkModel;
use CodeIgniter\Database\BaseBuilder;

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
        private readonly int $userId,
    ) {}

    /**
     * Returns the size of the user's library visible to the current viewer.
     */
    public function countAllLinks(bool $includeHidden = false): int {
        return $this->linkTotalsQuery($includeHidden)->countAllResults();
    }

    /**
     * Returns the accumulated visits visible to the current viewer.
     */
    public function countAllClicks(bool $includeHidden = false): int {
        $result = $this->linkTotalsQuery($includeHidden)
            ->selectSum('links.click_count', 'total_clicks')
            ->get()
            ->getRowArray();

        return (int) ($result['total_clicks'] ?? 0);
    }

    /**
     * Builds the shared totals query and prevents hidden collection contents
     * from leaking through the public link and click counters.
     */
    private function linkTotalsQuery(bool $includeHidden): BaseBuilder {
        $builder = db_connect()
            ->table('links')
            ->where('links.user_id', $this->userId)
            ->where('links.deleted_at', null);

        if ($includeHidden) {
            return $builder;
        }

        return $builder
            ->join('collections', 'collections.id = links.collection_id', 'left')
            ->where('links.is_hidden', 0)
            ->groupStart()
                ->where('links.collection_id', null)
                ->orGroupStart()
                    ->where('collections.user_id', $this->userId)
                    ->where('collections.is_hidden', 0)
                ->groupEnd()
            ->groupEnd();
    }

    /**
     * Returns the collections available in the collection filter.
     */
    public function getFilterOptions(bool $includeHidden = false): array {
        $collectionModel = new CollectionModel();

        $collectionModel->where('user_id', $this->userId);
        $this->applyCollectionVisibilityFilter($collectionModel, $includeHidden);

        $collections = $collectionModel
            ->select('id, name, icon')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        if ($collections === []) {
            return [];
        }

        $linkModel = (new LinkModel())
            ->where('user_id', $this->userId)
            ->whereIn('collection_id', array_column($collections, 'id'));

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
     * Retrieves the user's collections and links matching the selected hub filters.
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
        $linkModel = (new LinkModel())
            ->where('user_id', $this->userId)
            ->where('collection_id', null);

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
     * Searches the user's links without applying any of the hub filters.
     */
    public function search(string $term, bool $includeHidden = false): array {
        $collections = $this->findCollections([], $includeHidden);
        $result = [];

        if ($collections !== []) {
            $linkModel = $this->linksIn($collections);

            $this->applyVisibilityFilter($linkModel, $includeHidden);
            $this->applySearchTerm($linkModel, $term);

            $result = $this->attachLinks($collections, $linkModel->findAll());
        }

        $unassignedLinks = (new LinkModel())
            ->where('user_id', $this->userId)
            ->where('collection_id', null);

        $this->applyVisibilityFilter($unassignedLinks, $includeHidden);
        $this->applySearchTerm($unassignedLinks, $term);
        $unassigned = $unassignedLinks->findAll();

        if ($unassigned !== []) {
            $result[] = [
                'id'         => null,
                'name'       => 'Unassigned Links',
                'is_virtual' => true,
                'links'      => $this->prepareLinksForDisplay($unassigned),
            ];
        }

        return $result;
    }

    /**
     * Finds collections that match the selected collection scope.
     */
    private function findCollections(array $filters = [], bool $includeHidden = false): array {
        $collectionModel = new CollectionModel();

        $collectionModel->where('user_id', $this->userId);
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
     * Excludes hidden collections unless the caller may view them.
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
        return (new LinkModel())
            ->where('user_id', $this->userId)
            ->whereIn('collection_id', array_column($collections, 'id'));
    }

    /**
     * Excludes hidden links unless the caller may view them.
     */
    private function applyVisibilityFilter(LinkModel $linkModel, bool $includeHidden): void {
        if (! $includeHidden) {
            $linkModel->where('is_hidden', 0);
        }
    }

    private function applySearchTerm(LinkModel $linkModel, string $term): void {
        $linkModel
            ->groupStart()
            ->like('title', $term)
            ->orLike('url', $term)
            ->orLike('description', $term)
            ->groupEnd()
            ->orderBy('title', 'ASC');
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
