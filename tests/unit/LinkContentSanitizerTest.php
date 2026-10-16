<?php

use App\Libraries\LinkContentSanitizer;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LinkContentSanitizerTest extends CIUnitTestCase
{
    public function testEmptyDocumentsReturnNull(): void
    {
        $sanitizer = new LinkContentSanitizer();

        $this->assertNull($sanitizer->sanitize(''));
        $this->assertNull($sanitizer->sanitize('<p><br></p>'));
    }

    public function testSupportedTiptapMarkupIsPreserved(): void
    {
        $input = '<h2>Guide</h2><p><strong>Bold</strong> <em>italic</em> <u>underlined</u> <s>struck</s></p>'
            . '<ul><li>One</li></ul><ol><li>Two</li></ol><blockquote>Quote</blockquote>'
            . '<pre><code>echo true;</code></pre><hr>';

        $this->assertSame(
            str_replace('<hr>', '<hr />', $input),
            (new LinkContentSanitizer())->sanitize($input),
        );
    }

    public function testDangerousAndUnsupportedMarkupIsRemoved(): void
    {
        $result = (new LinkContentSanitizer())->sanitize(
            '<h2 onclick="alert(1)">Guide</h2><script>alert(1)</script>'
            . '<p style="color:red"><strong>Safe</strong><img src="x" onerror="alert(1)"></p>',
        );

        $this->assertSame('<h2>Guide</h2><p><strong>Safe</strong></p>', $result);
    }

    public function testHorizontalRuleCountsAsContent(): void
    {
        $this->assertSame('<hr />', (new LinkContentSanitizer())->sanitize('<hr>'));
    }
}
