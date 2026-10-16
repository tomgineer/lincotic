<?php

use App\Services\PageMetaService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PageMetaServiceTest extends CIUnitTestCase
{
    private PageMetaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PageMetaService();
    }

    public function testCollectionUsesItsNameDescriptionOwnerAndCanonicalUrl(): void
    {
        $meta = $this->service->forHub(
            'civic',
            'https://example.com/@civic',
            [
                'id'          => 42,
                'name'        => 'Financial News',
                'description' => 'Carefully selected financial reporting.',
            ],
        );

        $this->assertSame('Financial News by Civic | Lincotic', $meta['title']);
        $this->assertSame('Carefully selected financial reporting.', $meta['description']);
        $this->assertSame('https://example.com/@civic?collection=42', $meta['canonical']);
        $this->assertSame('index,follow', $meta['robots']);
    }

    public function testCollectionWithoutDescriptionGetsAUsefulFallback(): void
    {
        $meta = $this->service->forHub(
            'civic',
            'https://example.com/@civic',
            ['id' => 42, 'name' => 'Financial News', 'description' => '  '],
        );

        $this->assertSame(
            'Explore Financial News, a link collection curated by Civic on Lincotic.',
            $meta['description'],
        );
    }

    public function testFeaturedAndUnassignedViewsHaveDistinctMetadata(): void
    {
        $featured = $this->service->forHub('tom', 'https://example.com/@tom');
        $unassigned = $this->service->forHub('tom', 'https://example.com/@tom', unassigned: true);

        $this->assertSame('Featured links by Tom | Lincotic', $featured['title']);
        $this->assertSame('https://example.com/@tom', $featured['canonical']);
        $this->assertSame('Unassigned links by Tom | Lincotic', $unassigned['title']);
        $this->assertSame('https://example.com/@tom?collection=unassigned', $unassigned['canonical']);
    }

    public function testSearchMetadataIncludesTheTermAndPreventsIndexing(): void
    {
        $meta = $this->service->forSearch(
            'civic',
            'https://example.com/@civic/search',
            'markets & finance',
        );

        $this->assertSame('Search results for “markets & finance” by Civic | Lincotic', $meta['title']);
        $this->assertSame(
            'https://example.com/@civic/search?q=markets%20%26%20finance',
            $meta['canonical'],
        );
        $this->assertSame('noindex,follow', $meta['robots']);
    }
}
