<?php

namespace App\Services;

/**
 * Builds page metadata from data that has already been loaded by a controller.
 */
final class PageMetaService {

    private const string SITE_NAME = 'Lincotic';

    /**
     * Builds indexable metadata for a hub, collection, or unassigned-links page.
     *
     * @param array{name?: mixed, description?: mixed, id?: mixed}|null $collection
     *
     * @return array{title: string, description: string, canonical: string, robots: string}
     */
    public function forHub(
        string $username,
        string $hubUrl,
        ?array $collection = null,
        bool $unassigned = false,
    ): array {
        $owner = $this->displayUsername($username);

        if ($collection !== null) {
            $name = trim((string) ($collection['name'] ?? ''));

            if ($name !== '') {
                $description = trim((string) ($collection['description'] ?? ''));
                $id = (int) ($collection['id'] ?? 0);

                return [
                    'title'       => sprintf('%s by %s | %s', $name, $owner, self::SITE_NAME),
                    'description' => $description !== ''
                        ? $description
                        : sprintf(
                            'Explore %s, a link collection curated by %s on %s.',
                            $name,
                            $owner,
                            self::SITE_NAME,
                        ),
                    'canonical'   => $id > 0
                        ? $this->withQuery($hubUrl, ['collection' => (string) $id])
                        : $hubUrl,
                    'robots'      => 'index,follow',
                ];
            }
        }

        if ($unassigned) {
            return [
                'title'       => sprintf('Unassigned links by %s | %s', $owner, self::SITE_NAME),
                'description' => sprintf('Browse unassigned links by %s on %s.', $owner, self::SITE_NAME),
                'canonical'   => $this->withQuery($hubUrl, ['collection' => 'unassigned']),
                'robots'      => 'index,follow',
            ];
        }

        return [
            'title'       => sprintf('Featured links by %s | %s', $owner, self::SITE_NAME),
            'description' => sprintf('Explore featured links curated by %s on %s.', $owner, self::SITE_NAME),
            'canonical'   => $hubUrl,
            'robots'      => 'index,follow',
        ];
    }

    /**
     * Builds non-indexable metadata for a hub search-results page.
     *
     * @return array{title: string, description: string, canonical: string, robots: string}
     */
    public function forSearch(string $username, string $searchUrl, string $search): array {
        $owner = $this->displayUsername($username);
        $term = trim($search);

        return [
            'title'       => sprintf('Search results for “%s” by %s | %s', $term, $owner, self::SITE_NAME),
            'description' => sprintf('Search the links curated by %s on %s.', $owner, self::SITE_NAME),
            'canonical'   => $this->withQuery($searchUrl, ['q' => $term]),
            'robots'      => 'noindex,follow',
        ];
    }

    private function displayUsername(string $username): string {
        $username = trim($username);

        return $username === '' ? self::SITE_NAME : ucfirst($username);
    }

    /**
     * Appends an RFC 3986-encoded query string to a canonical URL.
     *
     * @param array<string, string> $query
     */
    private function withQuery(string $url, array $query): string {
        return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query(
            $query,
            '',
            '&',
            PHP_QUERY_RFC3986,
        );
    }
}
