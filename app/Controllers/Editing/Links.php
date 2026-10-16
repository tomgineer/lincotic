<?php

namespace App\Controllers\Editing;

use App\Services\Editing\EditStatus;
use App\Services\Editing\LinkEditor;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class Links extends EditingController {

    /**
     * Validates and creates a link for the current user.
     */
    public function create(): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        $userId = $this->authenticatedUserId();

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new LinkEditor())->create($payload, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create link: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the link right now.', 500);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        $this->invalidateGuestPageCache();

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message' => 'Link added.',
                'csrf'    => csrf_hash(),
            ]);
    }

    /**
     * Returns fresh link data for the editing modal.
     */
    public function show(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $userId = $this->authenticatedUserId();

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $link = (new LinkEditor())->get($id, $userId);
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
     * Validates and persists changes to an existing link.
     */
    public function update(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        $userId = $this->authenticatedUserId();

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new LinkEditor())->update($id, $payload, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update link {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the link right now.', 500);
        }

        if ($result['status'] === EditStatus::NOT_FOUND) {
            return $this->jsonError('Link not found.', 404);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        $this->invalidateGuestPageCache();

        return $this->response->setJSON([
            'message' => 'Link updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Permanently deletes an existing link.
     */
    public function delete(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $userId = $this->authenticatedUserId();

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new LinkEditor())->delete($id, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to delete link {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to delete the link right now.', 500);
        }

        if ($result['status'] === EditStatus::NOT_FOUND) {
            return $this->jsonError('Link not found.', 404);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->jsonError('Unable to delete the link right now.', 500);
        }

        $this->invalidateGuestPageCache();

        return $this->response->setJSON([
            'message' => 'Link deleted.',
            'csrf'    => csrf_hash(),
        ]);
    }

}
