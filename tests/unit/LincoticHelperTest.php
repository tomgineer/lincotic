<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LincoticHelperTest extends CIUnitTestCase
{
    public function testDisplayUrlRemovesHttpPrefixAndWww(): void
    {
        $this->assertSame(
            'example.com/articles/testing',
            display_url('https://www.example.com/articles/testing'),
        );

        $this->assertSame('example.com', display_url('http://example.com'));
    }

    public function testDisplayUrlRemovesTrailingSlashes(): void
    {
        $this->assertSame('example.com', display_url('https://www.example.com/'));
        $this->assertSame('example.com/articles', display_url('https://example.com/articles///'));
    }

    public function testDisplayUrlTruncatesToTheRequestedLength(): void
    {
        $displayUrl = display_url('https://www.example.com/a-very-long-path', 20);

        $this->assertSame('example.com/a-ver...', $displayUrl);
        $this->assertSame(20, mb_strlen($displayUrl));
    }

    public function testDisplayUrlHandlesShortLimits(): void
    {
        $this->assertSame('..', display_url('https://example.com', 2));
        $this->assertSame('', display_url('https://example.com', 0));
    }
}
