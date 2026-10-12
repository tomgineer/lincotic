<?php

namespace App\Controllers;

use App\Libraries\HashGenerator;
use App\Libraries\PasswordGenerator;
use CodeIgniter\HTTP\ResponseInterface;

final class Utilities extends BaseController {

    private const int MAX_HASH_INPUT_BYTES = 100_000;

    /**
     * Generates and displays a fresh batch of standard and strong passwords.
     */
    public function passwordGenerator(): string {
        $this->response->noCache();

        $generator = new PasswordGenerator();
        $passwords = [];

        foreach (range(5, 50, 5) as $length) {
            $passwords[] = [
                'length'   => $length,
                'standard' => $generator->generate($length),
                'strong'   => $generator->generate($length, true),
            ];
        }

        return view('site/utilities/password_generator', ['passwords' => $passwords]);
    }

    /**
     * Displays the live date, time, milestone, and calendar counters.
     */
    public function dateTimeCalculator(): string {
        return view('site/utilities/date_time_calculator');
    }

    /**
     * Displays the hashing utility and its supported algorithms.
     */
    public function hashGenerator(): string {
        return view('site/utilities/hash_generator', [
            'algorithms' => (new HashGenerator())->algorithms(),
        ]);
    }

    /**
     * Validates an AJAX request and returns hashes for the supplied value.
     */
    public function generateHashes(): ResponseInterface {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'AJAX requests only.']);
        }

        $payload = $this->request->getJSON(true);
        $value   = is_array($payload) ? ($payload['value'] ?? null) : null;

        if (! is_string($value)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['error' => 'A string value is required.']);
        }

        if (strlen($value) > self::MAX_HASH_INPUT_BYTES) {
            return $this->response
                ->setStatusCode(413)
                ->setJSON(['error' => 'Input must not exceed 100 KB.']);
        }

        return $this->response->setJSON([
            'hashes' => (new HashGenerator())->generate($value),
            'csrf'   => csrf_hash(),
        ]);
    }

    /**
     * Displays the browser-local work time tracker.
     */
    public function workTimeTracker(): string {
        return view('site/utilities/work_time_tracker');
    }
}
