<?php

namespace App\Services\CronTasks;

use RuntimeException;

/**
 * Ensures robots.txt exists and protects non-indexable application routes.
 */
final class CheckRobotsTxt {
    private readonly string $robotsPath;

    public function __construct(?string $publicPath = null) {
        $publicPath       = rtrim($publicPath ?? FCPATH, '/\\');
        $this->robotsPath = $publicPath . DIRECTORY_SEPARATOR . 'robots.txt';
    }

    public function run(): void {
        if (is_file($this->robotsPath)) {
            $contents = file_get_contents($this->robotsPath);

            if ($contents !== false && $this->isValid($contents)) {
                return;
            }
        }

        if (file_put_contents($this->robotsPath, $this->contents(), LOCK_EX) === false) {
            throw new RuntimeException('Could not write ' . $this->robotsPath . '.');
        }
    }

    private function isValid(string $contents): bool {
        $inWildcardGroup = false;
        $rules           = [];

        foreach (preg_split('/\R/u', $contents) ?: [] as $line) {
            $line = trim((string) preg_replace('/\s*#.*$/u', '', $line));

            if ($line === '' || ! str_contains($line, ':')) {
                continue;
            }

            [$directive, $value] = array_map('trim', explode(':', $line, 2));
            $directive           = strtolower($directive);

            if ($directive === 'user-agent') {
                $inWildcardGroup = $value === '*';
                continue;
            }

            if ($inWildcardGroup && in_array($directive, ['allow', 'disallow'], true)) {
                $rules[$directive . ':' . $value] = true;
            }
        }

        return isset(
            $rules['allow:/'],
            $rules['disallow:/edit/'],
            $rules['disallow:/go/'],
            $rules['disallow:/login'],
            $rules['disallow:/logout'],
        );
    }

    private function contents(): string {
        return implode(PHP_EOL, [
            'User-agent: *',
            'Allow: /',
            'Disallow: /edit/',
            'Disallow: /go/',
            'Disallow: /login',
            'Disallow: /logout',
            '',
        ]);
    }
}
