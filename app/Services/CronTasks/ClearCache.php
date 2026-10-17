<?php

namespace App\Services\CronTasks;

use RuntimeException;

/**
 * Clears every item managed by the configured CodeIgniter cache handler.
 */
final class ClearCache {
    /**
     * Clears the configured application cache and fails when cleanup is rejected.
     */
    public function run(): void {
        if (! cache()->clean()) {
            throw new RuntimeException('Could not clean the application cache.');
        }
    }
}
