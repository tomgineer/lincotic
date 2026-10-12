<?php

namespace App\Controllers;

use App\Services\EditService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class Edit extends BaseController
{
    /**
     * Returns fresh link data for the editor.
     */
    public function link(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        try {
            $link = (new EditService())->getLink($id);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to load link {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to load the link right now.', 500);
        }

        if ($link === null) {
            return $this->jsonError('Link not found.', 404);
        }

        return $this->response->setJSON([
            'link' => $link,
            'csrf' => csrf_hash(),
        ]);
    }

    /**
     * Validates and persists changes from the link editor.
     */
    public function updateLink(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->jsonError('Invalid request payload.', 422);
        }

        try {
            $result = (new EditService())->updateLink($id, $payload);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update link {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the link right now.', 500);
        }

        if ($result['status'] === EditService::NOT_FOUND) {
            return $this->jsonError('Link not found.', 404);
        }

        if ($result['status'] === EditService::INVALID) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'error'  => 'Please check the highlighted fields.',
                    'errors' => $result['errors'],
                    'csrf'   => csrf_hash(),
                ]);
        }

        return $this->response->setJSON([
            'message' => 'Link updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Soft deletes one link.
     */
    public function deleteLink(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        try {
            $result = (new EditService())->deleteLink($id);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to delete link {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to delete the link right now.', 500);
        }

        if ($result['status'] === EditService::NOT_FOUND) {
            return $this->jsonError('Link not found.', 404);
        }

        if ($result['status'] === EditService::INVALID) {
            return $this->jsonError('Unable to delete the link right now.', 500);
        }

        return $this->response->setJSON([
            'message' => 'Link deleted.',
            'csrf'    => csrf_hash(),
        ]);
    }

    private function jsonError(string $message, int $status): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'error' => $message,
                'csrf'  => csrf_hash(),
            ]);
    }
}
