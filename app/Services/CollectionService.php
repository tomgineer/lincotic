<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\LinkModel;

final class CollectionService {

    private const array SORTS = [
        'title'      => ['title', 'ASC'],
        'title_desc' => ['title', 'DESC'],
        'recent'     => ['created_at', 'DESC'],
        'updated'    => ['updated_at', 'DESC'],
        'popular'    => ['click_count', 'DESC'],
        'visited'    => ['last_clicked_at', 'DESC'],
        'oldest'     => ['created_at', 'ASC'],
    ];

    /**
     * Returns the collections available in the collection filter.
     */
    public function getFilterOptions(): array {
        return (new CollectionModel())
            ->select('id, name')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Retrieves collections and links matching the selected home filters.
     */
    public function filter(array $filters): array {
        if ($filters['unassignedOnly'] ?? false) {
            return $this->findUnassignedLinks($filters);
        }

        $collections = $this->findCollections($filters);

        if ($collections === []) {
            return [];
        }

        $links = $this->findFilteredLinks($collections, $filters);

        return $this->attachLinks($collections, $links);
    }

    /**
     * Returns links that are no longer assigned to a collection as a virtual group.
     */
    private function findUnassignedLinks(array $filters): array {
        $linkModel = (new LinkModel())->where('collection_id', null);

        $this->applyFlagFilter($linkModel, $filters['flag'] ?? 'all');
        $this->applyImportanceFilter($linkModel, $filters['importance'] ?? 'all');
        $this->applySort($linkModel, $filters['sort'] ?? 'title');

        $links = $linkModel->findAll();

        if ($links === []) {
            return [];
        }

        return [[
            'id'         => null,
            'name'       => 'Unassigned Links',
            'slug'       => 'unassigned',
            'is_virtual' => true,
            'links'      => $links,
        ]];
    }

    /**
     * Searches all links without applying any of the home filters.
     */
    public function search(string $term): array {
        $collections = $this->findCollections();

        if ($collections === []) {
            return [];
        }

        $linkModel = $this->linksIn($collections);

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
    private function findCollections(array $filters = []): array {
        $collectionModel = new CollectionModel();

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
     * Finds links within the selected collections and applies all link filters.
     */
    private function findFilteredLinks(array $collections, array $filters): array {
        $linkModel = $this->linksIn($collections);

        $this->applyFlagFilter($linkModel, $filters['flag'] ?? 'all');
        $this->applyImportanceFilter($linkModel, $filters['importance'] ?? 'all');
        $this->applySort($linkModel, $filters['sort'] ?? 'title');

        return $linkModel->findAll();
    }

    private function linksIn(array $collections): LinkModel {
        return (new LinkModel())->whereIn(
            'collection_id',
            array_column($collections, 'id')
        );
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
        [$sortField, $sortDirection] = self::SORTS[$sort] ?? self::SORTS['title'];

        $linkModel->orderBy($sortField, $sortDirection);

        if ($sortField !== 'title') {
            $linkModel->orderBy('title', 'ASC');
        }
    }

    /**
     * Groups links by collection and removes collections with no matching links.
     */
    private function attachLinks(array $collections, array $links): array {
        $linksByCollection = [];

        foreach ($links as $link) {
            $linksByCollection[$link['collection_id']][] = $link;
        }

        $collectionsWithLinks = array_map(
            static fn(array $collection): array => [
                ...$collection,
                'links' => $linksByCollection[$collection['id']] ?? [],
            ],
            $collections
        );

        return array_values(array_filter(
            $collectionsWithLinks,
            static fn(array $collection): bool => $collection['links'] !== []
        ));
    }
}
