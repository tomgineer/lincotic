<?php

namespace App\Controllers\Editing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class EditingController extends BaseController {

    /**
     * Disables response caching and rejects non-AJAX editing requests.
     */
    protected function guardAjax(): ?ResponseInterface {
        $this->response->noCache();

        return $this->request->isAJAX()
            ? null
            : $this->jsonError('AJAX requests only.', 403);
    }

    /**
     * Decodes the JSON request body or returns a validation response.
     *
     * @return array<string, mixed>|ResponseInterface
     */
    protected function jsonPayload(): array|ResponseInterface {
        $payload = $this->request->getJSON(true);

        return is_array($payload)
            ? $payload
            : $this->jsonError('Invalid request payload.', 422);
    }

    /**
     * Builds the standard response for field validation failures.
     *
     * @param array<string, string> $errors
     */
    protected function validationError(array $errors): ResponseInterface {
        return $this->response
            ->setStatusCode(422)
            ->setJSON([
                'error'  => 'Please check the highlighted fields.',
                'errors' => $errors,
                'csrf'   => csrf_hash(),
            ]);
    }

    /**
     * Builds the standard JSON error response with a fresh CSRF token.
     */
    protected function jsonError(string $message, int $status): ResponseInterface {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'error' => $message,
                'csrf'  => csrf_hash(),
            ]);
    }
}
