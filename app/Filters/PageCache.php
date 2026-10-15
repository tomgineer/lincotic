<?php

namespace App\Filters;

use App\Services\HubPageCache;
use App\Services\HubOwnerResolver;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\Header;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Caches public hub pages under a username-specific key namespace.
 */
final class PageCache implements FilterInterface {

    private readonly HubPageCache $cache;
    private readonly HubOwnerResolver $owners;

    public function __construct(
        ?HubPageCache $cache = null,
        ?HubOwnerResolver $owners = null,
    ) {
        $this->cache = $cache ?? service('hubPageCache');
        $this->owners = $owners ?? service('hubOwnerResolver');
    }

    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface {
        $username = $this->cache->usernameFor($request);

        if (! $this->isEnabled()
            || $username === null
            || ! $request instanceof IncomingRequest
            || $this->owners->resolve($username) === null) {
            return null;
        }

        $cachedResponse = $this->cache->get($request, $username);

        if ($cachedResponse === null) {
            return null;
        }

        $response = service('response');

        foreach (array_keys($response->headers()) as $name) {
            $response->removeHeader($name);
        }

        foreach ($cachedResponse['headers'] as $name => $value) {
            $response->setHeader($name, $value);
        }

        $response->setBody($cachedResponse['output']);
        $response->setStatusCode(
            $cachedResponse['status'] ?? 200,
            $cachedResponse['reason'] ?? '',
        );

        return $response;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null,
    ): ?ResponseInterface {
        $username = $this->cache->usernameFor($request);

        if (! $this->isEnabled()
            || $username === null
            || ! $request instanceof IncomingRequest
            || $response instanceof DownloadResponse
            || $response instanceof RedirectResponse
            || ! in_array($response->getStatusCode(), config('Cache')->cacheStatusCodes, true)) {
            return null;
        }

        $headers = [];

        foreach ($response->headers() as $name => $value) {
            if ($value instanceof Header) {
                $headers[$name] = $value->getValueLine();
                continue;
            }

            foreach ($value as $header) {
                $headers[$name][] = $header->getValueLine();
            }
        }

        $this->cache->save(
            $request,
            $username,
            [
                'headers' => $headers,
                'output'  => $response->getBody(),
                'status'  => $response->getStatusCode(),
                'reason'  => $response->getReasonPhrase(),
            ],
            config('Lincotic')->cache['duration'],
        );

        return $response;
    }

    private function isEnabled(): bool {
        $cache = config('Lincotic')->cache;

        return ENVIRONMENT === 'production'
            && $cache['enabled']
            && ! auth()->loggedIn();
    }

}
