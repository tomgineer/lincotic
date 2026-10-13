<?php

namespace App\Libraries;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class SvgIconSanitizer
{
    private const int MAX_LENGTH = 65_535;

    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $presentationAttributes = [
            'fill',
            'fill-rule',
            'clip-rule',
            'stroke',
            'stroke-width',
            'stroke-linecap',
            'stroke-linejoin',
            'opacity',
            'transform',
        ];

        $config = (new HtmlSanitizerConfig())
            ->withMaxInputLength(self::MAX_LENGTH)
            ->allowElement('svg', ['xmlns', 'viewbox'])
            ->allowElement('g', $presentationAttributes)
            ->allowElement('path', [...$presentationAttributes, 'd'])
            ->allowElement('circle', [...$presentationAttributes, 'cx', 'cy', 'r'])
            ->allowElement('ellipse', [...$presentationAttributes, 'cx', 'cy', 'rx', 'ry'])
            ->allowElement('rect', [...$presentationAttributes, 'x', 'y', 'width', 'height', 'rx', 'ry'])
            ->allowElement('line', [...$presentationAttributes, 'x1', 'y1', 'x2', 'y2'])
            ->allowElement('polyline', [...$presentationAttributes, 'points'])
            ->allowElement('polygon', [...$presentationAttributes, 'points'])
            ->forceAttribute('svg', 'xmlns', 'http://www.w3.org/2000/svg')
            ->forceAttribute('svg', 'viewbox', '0 0 24 24')
            ->forceAttribute('svg', 'fill', 'none')
            ->forceAttribute('svg', 'stroke', 'currentColor')
            ->forceAttribute('svg', 'stroke-width', '2')
            ->forceAttribute('svg', 'stroke-linecap', 'round')
            ->forceAttribute('svg', 'stroke-linejoin', 'round');

        $this->sanitizer = new HtmlSanitizer($config);
    }

    /**
     * Returns a canonical, safe inline SVG or null for an empty value.
     */
    public function sanitize(?string $input): ?string
    {
        $input = trim((string) $input);

        if ($input === '') {
            return null;
        }

        if (strlen($input) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The SVG icon is too large.');
        }

        $source = $this->parseXml($input);

        if ($source->doctype !== null
            || ! $source->documentElement instanceof DOMElement
            || strtolower($source->documentElement->localName) !== 'svg') {
            throw new InvalidArgumentException('Enter a valid SVG icon.');
        }

        $sanitized = $this->sanitizer->sanitize($input);
        $document  = $this->parseXml($sanitized);
        $root      = $document->documentElement;

        if (! $root instanceof DOMElement || strtolower($root->localName) !== 'svg') {
            throw new InvalidArgumentException('Enter a valid SVG icon.');
        }

        $this->removeTextAndComments($document);

        if (! $this->containsDrawingElement($root)) {
            throw new InvalidArgumentException('The SVG icon must contain a supported shape.');
        }

        // Symfony parses fragments as HTML and therefore normalizes viewBox to
        // lowercase. Restore the canonical SVG attribute name before storage.
        $root->removeAttribute('viewbox');
        $root->setAttribute('viewBox', '0 0 24 24');

        $output = $document->saveXML($root);

        if (! is_string($output) || $output === '') {
            throw new InvalidArgumentException('Unable to process the SVG icon.');
        }

        return $output;
    }

    private function parseXml(string $input): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadXML(
                $input,
                LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING,
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $loaded) {
            throw new InvalidArgumentException('Enter a valid SVG icon.');
        }

        return $document;
    }

    private function removeTextAndComments(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        $nodes = $xpath->query('//text() | //comment()');

        if ($nodes === false) {
            return;
        }

        $toRemove = [];

        foreach ($nodes as $node) {
            $toRemove[] = $node;
        }

        foreach ($toRemove as $node) {
            $node->parentNode?->removeChild($node);
        }
    }

    private function containsDrawingElement(DOMElement $root): bool
    {
        foreach ($root->getElementsByTagName('*') as $element) {
            if ($element instanceof DOMNode
                && in_array(strtolower($element->localName), [
                    'path',
                    'circle',
                    'ellipse',
                    'rect',
                    'line',
                    'polyline',
                    'polygon',
                ], true)) {
                return true;
            }
        }

        return false;
    }
}
