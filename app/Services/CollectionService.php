<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\LinkModel;

final class CollectionService {

    public function getFilterOptions(): array {
        return (new CollectionModel())
            ->select('id, name')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getWithLinks(array $filters = []): array {
        helper('array');

        $collectionModel = new CollectionModel();

        // Collection filters
        if ($filters['featuredOnly'] ?? false) {
            $collectionModel->where('is_featured', 1);
        }

        if (($filters['collectionId'] ?? null) !== null) {
            $collectionModel->where('id', $filters['collectionId']);
        }

        $collections = $collectionModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        if ($collections === []) {
            return [];
        }

        $linkModel = new LinkModel();

        // Fetch only links belonging to these collections
        $linkModel->whereIn(
            'collection_id',
            array_column($collections, 'id')
        );

        // Link filters
        $flag = $filters['flag'] ?? 'all';

        if ($flag === 'favorite') {
            $linkModel->where('is_favorite', 1);
        } elseif ($flag === 'pending_review') {
            $linkModel->where('is_pending_review', 1);
        } elseif ($flag === 'unflagged') {
            $linkModel
                ->where('is_favorite', 0)
                ->where('is_pending_review', 0);
        }

        $importance = $filters['importance'] ?? 'all';

        if (in_array($importance, ['high', 'normal', 'low'], true)) {
            $linkModel->where('importance', $importance);
        } elseif ($importance === 'none') {
            $linkModel->where('importance', null);
        }

        [$sortField, $sortDirection] = match ($filters['sort'] ?? 'title') {
            'title_desc' => ['title', 'DESC'],
            'recent'     => ['created_at', 'DESC'],
            'updated'    => ['updated_at', 'DESC'],
            'popular'    => ['click_count', 'DESC'],
            'visited'    => ['last_clicked_at', 'DESC'],
            'oldest'     => ['created_at', 'ASC'],
            default      => ['title', 'ASC'],
        };

        $linkModel->orderBy($sortField, $sortDirection);

        if ($sortField !== 'title') {
            $linkModel->orderBy('title', 'ASC');
        }

        $links = $linkModel->findAll();

        $linksByCollection = array_group_by($links, ['collection_id']);

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
