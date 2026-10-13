<?php

namespace App\Libraries;

/**
 * Builds a short, human-friendly version of a URL for display purposes.
 *
 * The original URL remains untouched and should still be used as the link's
 * destination. This class only prepares the text shown to the user.
 */
final class UrlFormatter {

    public function forDisplay(string $url, int $maxLength = 40): string {
        // Remove whitespace pasted before or after the URL.
        $displayUrl = trim($url);

        // Protocols and the common "www." prefix add noise in a compact card.
        $displayUrl = preg_replace(
            '#^(?:(?:https?:)?//)?(?:www\.)?#i',
            '',
            $displayUrl,
        ) ?? '';

        // Query parameters can be long and usually do not help identify a link.
        $queryPosition = strpos($displayUrl, '?');

        if ($queryPosition !== false) {
            $displayUrl = substr($displayUrl, 0, $queryPosition);
        }

        // A trailing slash does not add useful information to the display text.
        $displayUrl = rtrim($displayUrl, '/');

        // Apply the length limit only after every URL-specific cleanup is done.
        return $this->truncate($displayUrl, $maxLength);
    }

    private function truncate(string $value, int $maxLength): string {
        if ($maxLength < 1) {
            return '';
        }

        if (mb_strlen($value) <= $maxLength) {
            return $value;
        }

        // Very small limits can contain only the requested number of dots.
        if ($maxLength <= 3) {
            return mb_substr('...', 0, $maxLength);
        }

        return rtrim(mb_substr($value, 0, $maxLength - 3)) . '...';
    }
}
