<?php

namespace App\Libraries;

use InvalidArgumentException;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class LinkContentSanitizer {
    private const int MAX_LENGTH = 16_000_000;

    private HtmlSanitizer $sanitizer;

    public function __construct() {
        $config = (new HtmlSanitizerConfig())
            ->withMaxInputLength(self::MAX_LENGTH)
            ->allowElement('p')
            ->allowElement('h1')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('h5')
            ->allowElement('h6')
            ->allowElement('strong')
            ->allowElement('em')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('blockquote')
            ->allowElement('pre')
            ->allowElement('code')
            ->allowElement('br')
            ->allowElement('hr');

        $this->sanitizer = new HtmlSanitizer($config);
    }

    /**
     * Returns safe Tiptap HTML or null when the document has no content.
     */
    public function sanitize(?string $input): ?string {
        $input = trim((string) $input);

        if ($input === '') {
            return null;
        }

        if (strlen($input) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The documentation is too large.');
        }

        $sanitized = trim($this->sanitizer->sanitize($input));

        if (strlen($sanitized) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The documentation is too large.');
        }

        $text      = html_entity_decode(
            strip_tags($sanitized),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8',
        );
        $text = str_replace("\u{00A0}", ' ', $text);

        if (trim($text) === '' && ! str_contains($sanitized, '<hr')) {
            return null;
        }

        return $sanitized;
    }
}
