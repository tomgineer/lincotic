<?php

namespace App\Controllers\Editing;

use App\Services\Editing\EditStatus;
use App\Services\Editing\UserEditor;
use App\Services\UserQueryService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class Users extends EditingController {

    /**
     * Validates and creates a Shield user with the selected role and status.
     */
    public function create(): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        try {
            $result = (new UserEditor())->create($payload);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create user: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the user right now.', 500);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message' => 'User added.',
                'csrf'    => csrf_hash(),
            ]);
    }

    /**
     * Validates and updates an existing Shield user.
     */
    public function update(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        try {
            $cachedOwner = (new UserQueryService())->findById($id);
            $result = (new UserEditor())->update($id, $payload, (int) (auth()->user()->id ?? 0));
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update user {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the user right now.', 500);
        }

        if ($result['status'] === EditStatus::NOT_FOUND) {
            return $this->jsonError('User not found.', 404);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        if ($cachedOwner !== null) {
            $this->invalidateGuestPageCache($cachedOwner['username']);
        }

        return $this->response->setJSON([
            'message' => 'User updated.',
            'csrf'    => csrf_hash(),
        ]);
    }
}
