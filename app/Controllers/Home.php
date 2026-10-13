<?php

namespace App\Controllers;

use App\Services\LibraryQueryService;
use App\Services\UserQueryService;
use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController {

    public function index(): string {
        $this->cachePage(config('Lincotic')->cache['duration']);

        $service = new LibraryQueryService();
        $filters = $this->filters();

        return view('site/pages/home', [
            'collections'       => $service->filter($filters, auth()->loggedIn()),
            'collectionOptions' => $service->getFilterOptions(),
            'filters'           => $filters,
            'search'            => '',
            'adminUsers'        => $this->adminUsers(),
        ]);
    }

    /**
     * Runs an uncached global link search, independently from home filters.
     */
    public function search(): string|RedirectResponse {
        $searchInput = $this->request->getGet('q');
        $search      = is_string($searchInput) ? trim($searchInput) : '';

        if ($search === '') {
            return redirect()->to(site_url('/'));
        }

        $service = new LibraryQueryService();

        return view('site/pages/home', [
            'collections'       => $service->search($search, auth()->loggedIn()),
            'collectionOptions' => $service->getFilterOptions(),
            'filters'           => $this->defaultFilters(),
            'search'            => $search,
            'adminUsers'        => $this->adminUsers(),
        ]);
    }

    /**
     * Normalizes the home filter query independently from search input.
     */
    private function filters(): array {
        $collectionInput = $this->request->getGet('collection');
        $flagInput       = $this->request->getGet('flag');
        $importanceInput = $this->request->getGet('importance');
        $sortInput       = $this->request->getGet('sort');

        $collection = is_string($collectionInput) ? trim($collectionInput) : '';
        $flag       = is_string($flagInput) ? trim($flagInput) : '';
        $importance = is_string($importanceInput) ? trim($importanceInput) : '';
        $sort       = is_string($sortInput) ? trim($sortInput) : '';

        $collectionId = ctype_digit($collection) && (int) $collection > 0
            ? (int) $collection
            : null;
        $unassignedOnly = $collection === 'unassigned';
        $featuredOnly   = ! $unassignedOnly && ($collection === '' || $collection === 'featured');
        $collection     = $unassignedOnly
            ? 'unassigned'
            : ($featuredOnly
                ? 'featured'
                : ($collectionId !== null ? (string) $collectionId : 'all'));

        if (! in_array($flag, ['all', 'favorite', 'pending_review', 'unflagged'], true)) {
            $flag = 'all';
        }

        if (! in_array($importance, ['all', 'high', 'medium', 'low', 'default'], true)) {
            $importance = 'all';
        }

        if (! in_array($sort, ['title', 'title_desc', 'recent', 'updated', 'popular', 'visited', 'oldest'], true)) {
            $sort = 'title';
        }

        return [
            'collection'        => $collection,
            'collectionId'      => $collectionId,
            'featuredOnly'      => $featuredOnly,
            'unassignedOnly'    => $unassignedOnly,
            'flag'              => $flag,
            'importance'        => $importance,
            'sort'              => $sort,
        ];
    }

    private function defaultFilters(): array {
        return [
            'collection'        => 'all',
            'collectionId'      => null,
            'featuredOnly'      => false,
            'unassignedOnly'    => false,
            'flag'              => 'all',
            'importance'        => 'all',
            'sort'              => 'title',
        ];
    }

    private function adminUsers(): array {
        if (! auth()->loggedIn() || ! auth()->user()->inGroup('admin')) {
            return [];
        }

        return (new UserQueryService())->all();
    }
}
