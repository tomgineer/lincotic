<?php

namespace App\Controllers;

use App\Services\EditService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

final class Edit extends BaseController
{
    /**
     * Validates and creates one Shield user.
     */
    public function createUser(): ResponseInterface
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
            $result = (new EditService())->createUser($payload);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create user: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the user right now.', 500);
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

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message' => 'User added.',
                'csrf'    => csrf_hash(),
            ]);
    }

    /**
     * Validates and updates one Shield user.
     */
    public function updateUser(int $id): ResponseInterface
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
            $result = (new EditService())->updateUser($id, $payload, (int) (auth()->user()->id ?? 0));
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update user {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the user right now.', 500);
        }

        if ($result['status'] === EditService::NOT_FOUND) {
            return $this->jsonError('User not found.', 404);
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
            'message' => 'User updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Validates and creates one link.
     */
    public function createLink(): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->jsonError('Invalid request payload.', 422);
        }

        $userId = (int) (auth()->user()->id ?? 0);

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new EditService())->createLink($payload, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create link: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the link right now.', 500);
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

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message' => 'Link added.',
                'csrf'    => csrf_hash(),
            ]);
    }

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

    /**
     * Validates and creates one collection for the current user.
     */
    public function createCollection(): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->jsonError('Invalid request payload.', 422);
        }

        $userId = (int) (auth()->user()->id ?? 0);

        if ($userId < 1) {
            return $this->jsonError('Authenticated user not found.', 401);
        }

        try {
            $result = (new EditService())->createCollection($payload, $userId);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to create collection: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to add the collection right now.', 500);
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

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'message' => 'Collection added.',
                'csrf'    => csrf_hash(),
            ]);
    }

    /**
     * Returns fresh collection data for the editor.
     */
    public function collection(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        try {
            $collection = (new EditService())->getCollection($id);
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
     * Validates and persists changes from the collection editor.
     */
    public function updateCollection(int $id): ResponseInterface
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
            $result = (new EditService())->updateCollection($id, $payload);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to update collection {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to save the collection right now.', 500);
        }

        if ($result['status'] === EditService::NOT_FOUND) {
            return $this->jsonError('Collection not found.', 404);
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
            'message' => 'Collection updated.',
            'csrf'    => csrf_hash(),
        ]);
    }

    /**
     * Deletes one collection.
     */
    public function deleteCollection(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->jsonError('AJAX requests only.', 403);
        }

        try {
            $result = (new EditService())->deleteCollection($id);
        } catch (Throwable $exception) {
            log_message('error', 'Unable to delete collection {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError('Unable to delete the collection right now.', 500);
        }

        if ($result['status'] === EditService::NOT_FOUND) {
            return $this->jsonError('Collection not found.', 404);
        }

        if ($result['status'] === EditService::INVALID) {
            return $this->jsonError('Unable to delete the collection right now.', 500);
        }

        return $this->response->setJSON([
            'message' => 'Collection deleted.',
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
