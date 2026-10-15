<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use InvalidArgumentException;

/**
 * Owns the public hub cache namespace, keys and targeted invalidation.
 */
final class HubPageCache
{
    private const string KEY_PREFIX = 'hub_user_';

    private readonly CacheInterface $cache;
    private readonly string $rootUsername;

    public function __construct(
        ?CacheInterface $cache = null,
        ?string $rootUsername = null,
    ) {
        $this->cache = $cache ?? cache();
        $this->rootUsername = $rootUsername ?? (string) config('Lincotic')->rootUsername;
        $this->assertValidUsername($this->rootUsername);
    }

    public function usernameFor(RequestInterface $request): ?string
    {
        if (! $request instanceof IncomingRequest || $request->getMethod() !== 'GET') {
            return null;
        }

        $path = trim($request->getPath(), '/');

        if ($path === '') {
            return $this->rootUsername;
        }

        return preg_match('/\A@([a-z0-9]+)\z/', $path, $matches) === 1
            ? $matches[1]
            : null;
    }

    /**
     * @return array{headers: array<string, mixed>, output: string, status?: int, reason?: string}|null
     */
    public function get(IncomingRequest $request, string $username): ?array
    {
        $cached = $this->cache->get($this->keyFor($request, $username));

        if (! is_string($cached) || $cached === '') {
            return null;
        }

        $payload = unserialize($cached, ['allowed_classes' => false]);

        return is_array($payload)
            && isset($payload['headers'], $payload['output'])
            && is_array($payload['headers'])
            && is_string($payload['output'])
                ? $payload
                : null;
    }

    /**
     * @param array{headers: array<string, mixed>, output: string, status: int, reason: string} $payload
     */
    public function save(
        IncomingRequest $request,
        string $username,
        array $payload,
        int $ttl,
    ): bool {
        return $this->cache->save(
            $this->keyFor($request, $username),
            serialize($payload),
            $ttl,
        );
    }

    public function invalidate(string $username): int
    {
        $this->assertValidUsername($username);

        return $this->cache->deleteMatching($this->keyPrefix($username) . '*');
    }

    public function keyFor(IncomingRequest $request, string $username): string
    {
        $this->assertValidUsername($username);

        $uri = clone $request->getUri();
        $cacheQueryString = config('Cache')->cacheQueryString;
        $query = (bool) $cacheQueryString
            ? $uri->getQuery(is_array($cacheQueryString) ? ['only' => $cacheQueryString] : [])
            : '';
        $fingerprint = $request->getMethod() . ':' . $uri
            ->setFragment('')
            ->setQuery($query);

        return $this->keyPrefix($username) . md5($fingerprint);
    }

    private function keyPrefix(string $username): string
    {
        return self::KEY_PREFIX . $username . '_';
    }

    private function assertValidUsername(string $username): void
    {
        if (preg_match('/\A[a-z0-9]+\z/', $username) !== 1) {
            throw new InvalidArgumentException('Invalid hub cache username.');
        }
    }
}
