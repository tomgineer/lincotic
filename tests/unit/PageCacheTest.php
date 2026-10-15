<?php

use App\Services\HubPageCache;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PageCacheTest extends CIUnitTestCase
{
    public function testInvalidationDeletesOnlyTheSelectedUsersHubCache(): void
    {
        $cache = cache();
        $tomKey = 'hub_user_tom_test';
        $civicKey = 'hub_user_civic_test';

        $cache->save($tomKey, 'tom', 60);
        $cache->save($civicKey, 'civic', 60);

        try {
            $this->assertSame(1, (new HubPageCache($cache, 'civic'))->invalidate('tom'));
            $this->assertNull($cache->get($tomKey));
            $this->assertSame('civic', $cache->get($civicKey));
        } finally {
            $cache->delete($tomKey);
            $cache->delete($civicKey);
        }
    }

    public function testRootAndUserRoutesUseTheirOwnNamespaces(): void
    {
        $service = new HubPageCache(cache(), 'main');
        $root = $this->requestFor('/');
        $tom = $this->requestFor('/@tom');

        $this->assertSame('main', $service->usernameFor($root));
        $this->assertSame('tom', $service->usernameFor($tom));
        $this->assertStringStartsWith('hub_user_main_', $service->keyFor($root, 'main'));
        $this->assertStringStartsWith('hub_user_tom_', $service->keyFor($tom, 'tom'));
        $this->assertNotSame($service->keyFor($root, 'main'), $service->keyFor($tom, 'tom'));
    }

    public function testCachedPayloadRoundTripsWithinTheSelectedUserNamespace(): void
    {
        $cache = cache();
        $service = new HubPageCache($cache, 'main');
        $request = $this->requestFor('/@tom');
        $payload = [
            'headers' => ['Content-Type' => 'text/html'],
            'output'  => '<main>Tom</main>',
            'status'  => 200,
            'reason'  => 'OK',
        ];

        try {
            $this->assertTrue($service->save($request, 'tom', $payload, 60));
            $this->assertSame($payload, $service->get($request, 'tom'));
        } finally {
            $service->invalidate('tom');
        }
    }

    private function requestFor(string $path): IncomingRequest
    {
        $app = config('App');
        $uri = new SiteURI($app);
        $uri->setPath($path);

        $request = new IncomingRequest($app, $uri, null, new UserAgent());
        $request->setMethod('GET');

        return $request;
    }
}
