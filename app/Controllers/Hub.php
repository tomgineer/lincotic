<?php

namespace App\Controllers;

use App\Services\LibraryQueryService;
use App\Services\UserQueryService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Hub extends BaseController {

    private const string ROOT_USERNAME = 'civic';

    public function index(): string|RedirectResponse {
        $owner = $this->owner(self::ROOT_USERNAME);

        return $this->renderHub($owner, site_url('/'), site_url('search'));
    }

    public function show(string $username): string|RedirectResponse {
        $owner   = $this->owner($username);
        $hubPath = '@' . rawurlencode($owner['username']);

        return $this->renderHub(
            $owner,
            site_url($hubPath),
            site_url($hubPath . '/search'),
        );
    }

    public function search(): string|RedirectResponse {
        $owner = $this->owner(self::ROOT_USERNAME);

        return $this->renderSearch($owner, site_url('/'), site_url('search'));
    }

    public function searchUser(string $username): string|RedirectResponse {
        $owner   = $this->owner($username);
        $hubPath = '@' . rawurlencode($owner['username']);

        return $this->renderSearch(
            $owner,
            site_url($hubPath),
            site_url($hubPath . '/search'),
        );
    }

    /**
     * Renders the selected user's filtered hub.
     *
     * @param array{id: int, username: string} $owner
     */
    private function renderHub(array $owner, string $hubUrl, string $searchUrl): string|RedirectResponse {
        $collectionInput = $this->request->getGet('collection');
        $collection      = is_string($collectionInput) ? trim($collectionInput) : '';
        $isCollectionId  = ctype_digit($collection) && (int) $collection > 0;
        $isKnownView     = in_array($collection, ['featured', 'unassigned'], true);

        if ($collection !== '' && ! $isCollectionId && ! $isKnownView) {
            $query = $this->request->getGet();

            if (is_array($query)) {
                unset($query['collection']);
            }

            $location = $hubUrl;

            if (is_array($query) && $query !== []) {
                $location .= '?' . http_build_query($query);
            }

            return redirect()->to($location);
        }

        $this->cachePage(config('Lincotic')->cache['duration']);

        $service = new LibraryQueryService(service('urlFormatter'), $owner['id']);
        $filters = $this->filters();
        $includeHidden = $this->canSeeHiddenContent($owner['id']);

        return view('site/pages/hub', [
            'collections'       => $service->filter($filters, $includeHidden),
            'collectionOptions' => $service->getFilterOptions($includeHidden),
            'totalLinkCount'    => $service->countAllLinks($includeHidden),
            'totalClickCount'   => $service->countAllClicks($includeHidden),
            'filters'           => $filters,
            'search'            => '',
            'owner'             => $owner,
            'hubUrl'            => $hubUrl,
            'searchUrl'         => $searchUrl,
            'adminUsers'        => $this->adminUsers(),
        ]);
    }

    /**
     * Runs an uncached user-scoped link search independently from hub filters.
     *
     * @param array{id: int, username: string} $owner
     */
    private function renderSearch(array $owner, string $hubUrl, string $searchUrl): string|RedirectResponse {
        $searchInput = $this->request->getGet('q');
        $search      = is_string($searchInput) ? trim($searchInput) : '';

        if ($search === '') {
            return redirect()->to($hubUrl);
        }

        $service = new LibraryQueryService(service('urlFormatter'), $owner['id']);
        $includeHidden = $this->canSeeHiddenContent($owner['id']);

        return view('site/pages/hub', [
            'collections'       => $service->search($search, $includeHidden),
            'collectionOptions' => $service->getFilterOptions($includeHidden),
            'totalLinkCount'    => $service->countAllLinks($includeHidden),
            'totalClickCount'   => $service->countAllClicks($includeHidden),
            'filters'           => $this->defaultFilters(),
            'search'            => $search,
            'owner'             => $owner,
            'hubUrl'            => $hubUrl,
            'searchUrl'         => $searchUrl,
            'adminUsers'        => $this->adminUsers(),
        ]);
    }

    /**
     * @return array{id: int, username: string}
     */
    private function owner(string $username): array {
        $owner = (new UserQueryService())->findByUsername($username);

        if ($owner === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $owner;
    }

    private function canSeeHiddenContent(int $ownerId): bool {
        if (! auth()->loggedIn()) {
            return false;
        }

        $user = auth()->user();

        return (int) ($user->id ?? 0) === $ownerId || $user->inGroup('admin');
    }

    /**
     * Normalizes the hub filter query independently from search input.
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
        $featuredOnly   = ! $unassignedOnly && $collectionId === null;
        $collection     = $unassignedOnly
            ? 'unassigned'
            : ($collectionId !== null ? (string) $collectionId : 'featured');

        if (! in_array($flag, ['all', 'favorite', 'pending_review', 'unflagged'], true)) {
            $flag = 'all';
        }

        if (! in_array($importance, ['all', 'high', 'medium', 'low', 'default'], true)) {
            $importance = 'all';
        }

        if (! in_array($sort, ['importance', 'title', 'title_desc', 'recent', 'updated', 'popular', 'visited', 'oldest'], true)) {
            $sort = 'importance';
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
            'collection'        => 'featured',
            'collectionId'      => null,
            'featuredOnly'      => true,
            'unassignedOnly'    => false,
            'flag'              => 'all',
            'importance'        => 'all',
            'sort'              => 'importance',
        ];
    }

    private function adminUsers(): array {
        if (! auth()->loggedIn() || ! auth()->user()->inGroup('admin')) {
            return [];
        }

        return (new UserQueryService())->all();
    }
}
