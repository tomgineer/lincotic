<?php

use App\Libraries\UrlFormatter;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class UrlFormatterTest extends CIUnitTestCase
{
    public function testIsAvailableAsASharedApplicationService(): void
    {
        $this->assertInstanceOf(UrlFormatter::class, service('urlFormatter'));
        $this->assertSame(service('urlFormatter'), service('urlFormatter'));
    }

    public function testRemovesHttpPrefixAndWww(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame(
            'example.com/articles/testing',
            $formatter->forDisplay('https://www.example.com/articles/testing'),
        );

        $this->assertSame('example.com', $formatter->forDisplay('http://example.com'));
    }

    public function testRemovesTrailingSlashes(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame('example.com', $formatter->forDisplay('https://www.example.com/'));
        $this->assertSame(
            'example.com/articles',
            $formatter->forDisplay('https://example.com/articles///'),
        );
    }

    public function testRemovesTheQueryString(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame(
            'example.com/articles/testing',
            $formatter->forDisplay(
                'https://www.example.com/articles/testing?utm_source=test&preview=1',
            ),
        );

        $this->assertSame(
            'example.com',
            $formatter->forDisplay('https://example.com/?query=value'),
        );
    }

    public function testAppliesLengthLimitAfterRemovingQueryString(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame(
            'example.com/short',
            $formatter->forDisplay(
                'https://example.com/short?this=query-is-intentionally-very-long',
                24,
            ),
        );
    }

    public function testTruncatesToTheRequestedLength(): void
    {
        $formatter = new UrlFormatter();
        $displayUrl = $formatter->forDisplay(
            'https://www.example.com/a-very-long-path',
            20,
        );

        $this->assertSame('example.com/a-ver...', $displayUrl);
        $this->assertSame(20, mb_strlen($displayUrl));
    }

    public function testHandlesShortLimits(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame('..', $formatter->forDisplay('https://example.com', 2));
        $this->assertSame('', $formatter->forDisplay('https://example.com', 0));
    }

    public function testFormatsUrlThroughReadableCleanupPipeline(): void
    {
        $formatter = new UrlFormatter();

        $this->assertSame(
            'example.com/a-long-path...',
            $formatter->forDisplay(
                '  https://www.example.com/a-long-path-that-will-be-shortened/?tracking=value  ',
                26,
            ),
        );
    }
}
