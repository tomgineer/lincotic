<?php

// Body Id
if (! function_exists('body_id')) {
    function body_id(): string {
        $router = service('router');
        $controller = array_last(explode('\\', $router->controllerName()));
        return strtolower($controller . '-' . $router->methodName());
    }
}

// Formats a URL for compact display without changing its destination.
if (! function_exists('display_url')) {
    function display_url(string $url, int $maxLength = 40): string {
        $displayUrl = preg_replace(
            '#^(?:(?:https?:)?//)?(?:www\.)?#i',
            '',
            trim($url),
        ) ?? '';
        $displayUrl = rtrim($displayUrl, '/');

        if ($maxLength < 1) {
            return '';
        }

        if (mb_strlen($displayUrl) <= $maxLength) {
            return $displayUrl;
        }

        if ($maxLength <= 3) {
            return mb_substr('...', 0, $maxLength);
        }

        return rtrim(mb_substr($displayUrl, 0, $maxLength - 3)) . '...';
    }
}
