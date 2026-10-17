<?php

namespace App\Controllers\Editing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;
use Throwable;

abstract class EditingController extends BaseController {

    /**
     * Disables response caching and rejects non-AJAX editing requests.
     */
    protected function guardAjax(): ?ResponseInterface {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        $user = auth()->user();

        return $user instanceof User && ! $user->isBanned()
            ? null
            : $this->jsonError('Authenticated user not found.', 401);
    }

    /**
     * Returns the active authenticated user's identifier or zero when unavailable.
     */
    protected function authenticatedUserId(): int {
        $user = auth()->user();

        return $user instanceof User && ! $user->isBanned()
            ? (int) ($user->id ?? 0)
            : 0;
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
     * Invalidates cached guest pages after a successful editing operation.
     */
    protected function invalidateGuestPageCache(?string $username = null): void {
        if (ENVIRONMENT !== 'production' || ! config('Lincotic')->cache['enabled']) {
            return;
        }

        $username ??= (string) (auth()->user()->username ?? '');

        if (preg_match('/\A[a-z0-9]+\z/', $username) !== 1) {
            log_message('warning', 'Unable to clear guest page cache for an invalid username.');

            return;
        }

        try {
            service('hubPageCache')->invalidate($username);
        } catch (Throwable $exception) {
            log_message(
                'warning',
                'Unable to clear cached guest pages for {username}: {message}',
                [
                    'username' => $username,
                    'message'  => $exception->getMessage(),
                ],
            );
        }
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
