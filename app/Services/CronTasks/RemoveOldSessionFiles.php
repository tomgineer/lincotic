<?php

namespace App\Services\CronTasks;

use CodeIgniter\Session\Handlers\FileHandler;
use Config\Session;
use RuntimeException;

/**
 * Removes expired files from the configured session directory.
 */
final class RemoveOldSessionFiles {
    /**
     * Runs session garbage collection only when file-based sessions are configured.
     */
    public function run(): void {
        $config = config(Session::class);

        if ($config->driver !== FileHandler::class) {
            return;
        }

        $removed = session_gc();

        if ($removed === false) {
            throw new RuntimeException('Could not clean the session directory.');
        }
    }
}
