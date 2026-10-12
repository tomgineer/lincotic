<?php

namespace App\Filters;

use CodeIgniter\Filters\PageCache as BasePageCache;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enables page caching only for guests in production when it is enabled in
 * the Lincotic configuration.
 */
class PageCache extends BasePageCache {

    public function before(RequestInterface $request, $arguments = null) {
        if (! $this->isEnabled()) {
            return null;
        }

        return parent::before($request, $arguments);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        if (! $this->isEnabled()) {
            return null;
        }

        return parent::after($request, $response, $arguments);
    }

    private function isEnabled(): bool {
        $cache = config('Lincotic')->cache;

        return ENVIRONMENT === 'production'
            && $cache['enabled']
            && ! auth()->loggedIn();
    }
}
