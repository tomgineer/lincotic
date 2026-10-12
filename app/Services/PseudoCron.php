<?php

namespace App\Services;

use App\Services\CronTasks\CheckRobotsTxt;
use App\Services\CronTasks\ClearCache;
use App\Services\CronTasks\RemoveOldSessionFiles;

/**
 * Coordinates the application's lightweight maintenance tasks.
 */
final class PseudoCron {
    public function __construct(
        private readonly ClearCache $clearCache,
        private readonly RemoveOldSessionFiles $removeOldSessionFiles,
        private readonly CheckRobotsTxt $checkRobotsTxt,
    ) {
    }

    /**
     * Runs every registered task in sequence.
     */
    public function run(): void {
        $this->clearCache->run();
        $this->removeOldSessionFiles->run();
        $this->checkRobotsTxt->run();
    }
}
