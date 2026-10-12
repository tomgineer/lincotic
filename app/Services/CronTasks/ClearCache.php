<?php

namespace App\Services\CronTasks;

use RuntimeException;

/**
 * Clears every item managed by the configured CodeIgniter cache handler.
 */
final class ClearCache {
    public function run(): void {
        if (! cache()->clean()) {
            throw new RuntimeException('Could not clean the application cache.');
        }
    }
}
