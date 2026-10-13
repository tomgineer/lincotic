<?php

use App\Libraries\SvgIconSanitizer;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SvgIconSanitizerTest extends CIUnitTestCase
{
    public function testEmptyIconReturnsNull(): void
    {
        $this->assertNull((new SvgIconSanitizer())->sanitize('  '));
    }

    public function testTablerIconIsSanitizedAndCanonicalized(): void
    {
        $input = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18" /></svg>';

        $result = (new SvgIconSanitizer())->sanitize($input);

        $this->assertNotNull($result);
        $this->assertStringContainsString('viewBox="0 0 24 24"', $result);
        $this->assertStringContainsString('stroke="currentColor"', $result);
        $this->assertStringContainsString('d="M3 18h18"', $result);
        $this->assertStringNotContainsString('width="24"', $result);
        $this->assertStringNotContainsString('height="24"', $result);
        $this->assertStringNotContainsString('class=', $result);
    }

    public function testUnsafeSvgContentIsRemoved(): void
    {
        $input = '<svg viewBox="0 0 24 24" onload="alert(1)"><path d="M3 18h18" /><script>alert(1)</script><foreignObject><div>Unsafe</div></foreignObject></svg>';

        $result = (new SvgIconSanitizer())->sanitize($input);

        $this->assertNotNull($result);
        $this->assertStringContainsString('d="M3 18h18"', $result);
        $this->assertStringNotContainsString('onload', $result);
        $this->assertStringNotContainsString('script', $result);
        $this->assertStringNotContainsString('foreignObject', $result);
        $this->assertStringNotContainsString('Unsafe', $result);
    }

    /**
     * @dataProvider invalidSvgProvider
     */
    public function testInvalidSvgIsRejected(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new SvgIconSanitizer())->sanitize($input);
    }

    public static function invalidSvgProvider(): iterable
    {
        yield 'malformed XML' => ['<svg><path></svg>'];
        yield 'different root element' => ['<div><path d="M0 0" /></div>'];
        yield 'no supported shape' => ['<svg><script>alert(1)</script></svg>'];
        yield 'doctype' => ['<!DOCTYPE svg><svg><path d="M0 0" /></svg>'];
    }
}
