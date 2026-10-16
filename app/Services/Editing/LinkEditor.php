<?php

namespace App\Services\Editing;

use App\Models\CollectionModel;
use App\Models\LinkModel;

final class LinkEditor
{
    private const array RULES = [
        'title' => [
            'label' => 'Title',
            'rules' => 'required|max_length[255]',
        ],
        'url' => [
            'label' => 'URL',
            'rules' => 'required|max_length[2048]|valid_url_strict[http,https]',
        ],
        'collection_id' => [
            'label' => 'Collection',
            'rules' => 'permit_empty|is_natural_no_zero',
        ],
        'description' => [
            'label' => 'Description',
            'rules' => 'permit_empty|max_length[65535]',
        ],
        'importance' => [
            'label' => 'Importance',
            'rules' => 'permit_empty|in_list[low,medium,high]',
        ],
        'is_favorite' => [
            'label' => 'Favorite',
            'rules' => 'required|in_list[0,1]',
        ],
        'is_pending_review' => [
            'label' => 'Pending review',
            'rules' => 'required|in_list[0,1]',
        ],
        'is_hidden' => [
            'label' => 'Hidden',
            'rules' => 'required|in_list[0,1]',
        ],
    ];

    public function __construct(
        private readonly LinkModel $links = new LinkModel(),
        private readonly CollectionModel $collections = new CollectionModel(),
    ) {
    }

    /**
     * Returns the fields exposed by the link editor.
     */
    public function get(int $id, int $userId): ?array
    {
        $link = $this->findOwnedLink($id, $userId);

        return is_array($link) ? $this->present($link) : null;
    }

    /**
     * Validates and creates one link for the current user.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function create(array $input, int $userId): array
    {
        $prepared = $this->prepareData($input, true);

        if ($prepared['data'] === null) {
            return $this->result(EditStatus::INVALID, errors: $prepared['errors']);
        }

        $collection = $prepared['collection'];
        $data       = $prepared['data'];

        if ($userId < 1) {
            return $this->result(EditStatus::INVALID);
        }

        if ($data['collection_id'] !== null
            && (! is_array($collection) || (int) ($collection['user_id'] ?? 0) !== $userId)) {
            return $this->result(EditStatus::INVALID, errors: [
                'collection_id' => 'The selected collection is not available.',
            ]);
        }

        $data['user_id'] = $userId;

        if ($this->links->insert($data) === false) {
            return $this->result(EditStatus::INVALID, errors: $this->links->errors());
        }

        return $this->result(EditStatus::CREATED);
    }

    /**
     * Validates and updates one link without changing ownership or activity data.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function update(int $id, array $input, int $userId): array
    {
        if ($this->findOwnedLink($id, $userId) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        $prepared = $this->prepareData($input);

        if ($prepared['data'] === null) {
            return $this->result(EditStatus::INVALID, errors: $prepared['errors']);
        }

        if (! is_array($prepared['collection'])
            || (int) ($prepared['collection']['user_id'] ?? 0) !== $userId) {
            return $this->result(EditStatus::INVALID, errors: [
                'collection_id' => 'The selected collection is not available.',
            ]);
        }

        if (! $this->links->update($id, $prepared['data'])) {
            return $this->result(EditStatus::INVALID, errors: $this->links->errors());
        }

        return $this->result(EditStatus::UPDATED);
    }

    /**
     * Permanently deletes one link.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function delete(int $id, int $userId): array
    {
        if ($this->findOwnedLink($id, $userId) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        if (! $this->links->delete($id)) {
            return $this->result(EditStatus::INVALID, errors: $this->links->errors());
        }

        return $this->result(EditStatus::DELETED);
    }

    private function findOwnedLink(int $id, int $userId): ?array
    {
        $link = $this->links->find($id);

        return is_array($link) && (int) ($link['user_id'] ?? 0) === $userId
            ? $link
            : null;
    }

    /**
     * @return array{
     *     data: array<string, int|string|null>|null,
     *     collection: array|null,
     *     errors: array<string, string>
     * }
     */
    private function prepareData(array $input, bool $allowUnassigned = false): array
    {
        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::RULES);

        if (! $validation->run($data)) {
            return [
                'data'       => null,
                'collection' => null,
                'errors'     => $validation->getErrors(),
            ];
        }

        if ($data['collection_id'] === '') {
            if (! $allowUnassigned) {
                return [
                    'data'       => null,
                    'collection' => null,
                    'errors'     => [
                        'collection_id' => 'Select a collection.',
                    ],
                ];
            }

            $data['collection_id'] = null;

            return [
                'data'       => $data,
                'collection' => null,
                'errors'     => [],
            ];
        }

        $collectionId = (int) $data['collection_id'];
        $collection   = $this->collections->find($collectionId);

        if (! is_array($collection)) {
            return [
                'data'       => null,
                'collection' => null,
                'errors'     => [
                    'collection_id' => 'The selected collection is not available.',
                ],
            ];
        }

        $data['collection_id'] = $collectionId;

        return [
            'data'       => $data,
            'collection' => $collection,
            'errors'     => [],
        ];
    }

    private function normalizeInput(array $input): array
    {
        $importance  = $this->stringValue($input, 'importance');
        $description = $this->stringValue($input, 'description');

        return [
            'title'             => $this->stringValue($input, 'title'),
            'url'               => $this->stringValue($input, 'url'),
            'collection_id'     => $this->integerValue($input['collection_id'] ?? null),
            'description'       => $description === '' ? null : $description,
            'importance'        => $importance === '' ? null : $importance,
            'is_favorite'       => $this->booleanValue($input['is_favorite'] ?? false),
            'is_pending_review' => $this->booleanValue($input['is_pending_review'] ?? false),
            'is_hidden'         => $this->booleanValue($input['is_hidden'] ?? false),
        ];
    }

    private function present(array $link): array
    {
        return [
            'id'                => (int) $link['id'],
            'collection_id'     => $link['collection_id'] === null ? null : (int) $link['collection_id'],
            'title'             => (string) $link['title'],
            'url'               => (string) $link['url'],
            'description'       => (string) ($link['description'] ?? ''),
            'importance'        => (string) ($link['importance'] ?? ''),
            'is_favorite'       => (bool) $link['is_favorite'],
            'is_pending_review' => (bool) $link['is_pending_review'],
            'is_hidden'         => (bool) $link['is_hidden'],
        ];
    }

    private function stringValue(array $input, string $key): string
    {
        return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
    }

    private function integerValue(mixed $value): int|string
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }

        return is_string($value) && ctype_digit($value) ? $value : '';
    }

    private function booleanValue(mixed $value): int
    {
        return in_array($value, [true, 1, '1', 'true', 'on'], true) ? 1 : 0;
    }

    /**
     * @param array<string, string> $errors
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    private function result(string $status, ?array $link = null, array $errors = []): array
    {
        return [
            'status' => $status,
            'link'   => $link,
            'errors' => $errors,
        ];
    }
}
