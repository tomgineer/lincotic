<?php

namespace App\Controllers;

use App\Services\CollectionService;

class Home extends BaseController {

    public function index(): string {
        $cache = config('Lincotic')->cache;

        if ($cache['enabled'] && $cache['duration'] > 0) {
            $this->cachePage($cache['duration']);
        }

        $collectionInput = $this->request->getGet('collection');
        $flagInput       = $this->request->getGet('flag');
        $importanceInput = $this->request->getGet('importance');
        $sortInput       = $this->request->getGet('sort');
        $searchInput     = $this->request->getGet('q');

        $collection = is_string($collectionInput) ? trim($collectionInput) : '';
        $flag       = is_string($flagInput) ? trim($flagInput) : '';
        $importance = is_string($importanceInput) ? trim($importanceInput) : '';
        $sort       = is_string($sortInput) ? trim($sortInput) : '';
        $search     = is_string($searchInput) ? trim($searchInput) : '';

        $collectionId = ctype_digit($collection) && (int) $collection > 0
            ? (int) $collection
            : null;
        $featuredOnly = $collection === '' || $collection === 'featured';
        $collection   = $featuredOnly
            ? 'featured'
            : ($collectionId !== null ? (string) $collectionId : 'all');

        if (! in_array($flag, ['all', 'favorite', 'pending_review', 'unflagged'], true)) {
            $flag = 'all';
        }

        if (! in_array($importance, ['all', 'high', 'medium', 'low', 'default'], true)) {
            $importance = 'all';
        }

        if (! in_array($sort, ['title', 'title_desc', 'recent', 'updated', 'popular', 'visited', 'oldest'], true)) {
            $sort = 'title';
        }

        if ($search !== '') {
            $collection   = 'all';
            $collectionId = null;
            $featuredOnly = false;
            $flag         = 'all';
            $importance   = 'all';
            $sort         = 'title';
        }

        $service = new CollectionService();
        $filters = [
            'collection'   => $collection,
            'collectionId' => $collectionId,
            'featuredOnly' => $featuredOnly,
            'flag'         => $flag,
            'importance'   => $importance,
            'sort'         => $sort,
            'search'       => $search,
        ];

        return view('site/pages/home', [
            'collections'       => $service->getWithLinks($filters),
            'collectionOptions' => $service->getFilterOptions(),
            'filters'           => $filters,
        ]);
    }
}
