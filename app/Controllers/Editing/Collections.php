<?php

namespace App\Controllers\Editing;

use App\Services\Editing\CollectionEditor;
use App\Services\Editing\EditStatus;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class Collections extends EditingController {
    /**
     * Validates and creates a collection owned by the current user.
     */
    public function create(): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        $userId = (int) (auth()->user()->id ?? 0);

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new CollectionEditor())->create($payload, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create collection: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the collection right now.', 500);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        $this->invalidateGuestPageCache();

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message'    => 'Collection added.',
                'collection' => ['id' => $result['collection_id']],
                'csrf'       => csrf_hash(),
            ]);
    }

    /**
     * Validates and persists the display order of all collections.
     */
    public function reorder(): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        $payload = $this->jsonPayload();

        if ($payload instanceof ResponseInterface) {
            return $payload;
        }

        $collectionIds = $payload['collection_ids'] ?? null;

        if (! is_array($collectionIds)) {
            return $this->validationError([
                'collection_ids' => 'The complete collection order is required.',
            ]);
        }

        try {
            $result = (new CollectionEditor())->reorder($collectionIds);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to reorder collections: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the collection order right now.', 500);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        $this->invalidateGuestPageCache();

        return $this->response->setJSON([
            'message' => 'Collection order updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Returns fresh collection data for the editing modal.
     */
    public function show(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        try {
            $collection = (new CollectionEditor())->get($id);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to load collection {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to load the collection right now.', 500);
        }

        if ($collection === null) {
            return $this->jsonError('Collection not found.', 404);
        }

        return $this->response->setJSON([
            'collection' => $collection,
            'csrf'       => csrf_hash(),
        ]);
    }

    /**
     * Validates and persists changes to an existing collection.
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
            $result = (new CollectionEditor())->update($id, $payload);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update collection {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the collection right now.', 500);
        }

        if ($result['status'] === EditStatus::NOT_FOUND) {
            return $this->jsonError('Collection not found.', 404);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->validationError($result['errors']);
        }

        $this->invalidateGuestPageCache();

        return $this->response->setJSON([
            'message' => 'Collection updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Deletes an existing collection.
     */
    public function delete(int $id): ResponseInterface {
        if (($response = $this->guardAjax()) !== null) {
            return $response;
        }

        try {
            $result = (new CollectionEditor())->delete($id);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to delete collection {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to delete the collection right now.', 500);
        }

        if ($result['status'] === EditStatus::NOT_FOUND) {
            return $this->jsonError('Collection not found.', 404);
        }

        if ($result['status'] === EditStatus::INVALID) {
            return $this->jsonError('Unable to delete the collection right now.', 500);
        }

        $this->invalidateGuestPageCache();

        return $this->response->setJSON([
            'message' => 'Collection deleted.',
            'csrf'    => csrf_hash(),
        ]);
    }
}
