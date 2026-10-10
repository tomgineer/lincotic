<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\LinkModel;

final class CollectionService
{
    public function getWithLinks(array $filters = []): array
    {
        helper('array');

        $collectionModel = new CollectionModel();

        // Collection filters
        if ($filters['featuredOnly'] ?? false) {
            $collectionModel->where('is_featured', 1);
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
        if (array_key_exists('importance', $filters)) {
            $linkModel->where('importance', $filters['importance']);
        }

        $links = $linkModel
            ->orderBy('title', 'ASC')
            ->findAll();

        $linksByCollection = array_group_by($links, ['collection_id']);

        return array_map(
            static fn(array $collection): array => [
                ...$collection,
                'links' => $linksByCollection[$collection['id']] ?? [],
            ],
            $collections
        );
    }
}
