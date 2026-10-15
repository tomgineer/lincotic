<?php

namespace App\Services\Editing;

use App\Libraries\SvgIconSanitizer;
use App\Models\CollectionModel;
use InvalidArgumentException;

final class CollectionEditor
{
    private const array RULES = [
        'name' => [
            'label' => 'Name',
            'rules' => 'required|max_length[120]',
        ],
        'description' => [
            'label' => 'Description',
            'rules' => 'permit_empty|max_length[65535]',
        ],
        'icon' => [
            'label' => 'Icon SVG',
            'rules' => 'permit_empty|max_length[65535]',
        ],
        'is_featured' => [
            'label' => 'Featured',
            'rules' => 'required|in_list[0,1]',
        ],
        'is_hidden' => [
            'label' => 'Hidden',
            'rules' => 'required|in_list[0,1]',
        ],
    ];

    public function __construct(
        private readonly CollectionModel $collections = new CollectionModel(),
        private readonly SvgIconSanitizer $svgIconSanitizer = new SvgIconSanitizer(),
    ) {
    }

    /**
     * Returns the fields exposed by the collection editor.
     */
    public function get(int $id, int $userId): ?array
    {
        $collection = $this->findOwnedCollection($id, $userId);

        return is_array($collection) ? $this->present($collection) : null;
    }

    /**
     * Validates and creates one collection for the current user.
     *
     * @return array{status: string, errors: array<string, string>, collection_id?: int}
     */
    public function create(array $input, int $userId): array
    {
        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::RULES);

        if (! $validation->run($data)) {
            return $this->result(EditStatus::INVALID, $validation->getErrors());
        }

        try {
            $data['icon'] = $this->svgIconSanitizer->sanitize($data['icon']);
        } catch (InvalidArgumentException $exception) {
            return $this->result(EditStatus::INVALID, [
                'icon' => $exception->getMessage(),
            ]);
        }

        if ($userId < 1) {
            return $this->result(EditStatus::INVALID);
        }

        $data['user_id'] = $userId;

        $collectionId = $this->collections->insert($data);

        if ($collectionId === false) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return [
            ...$this->result(EditStatus::CREATED),
            'collection_id' => (int) $collectionId,
        ];
    }

    /**
     * Validates and updates the display order of the current user's collections.
     *
     * @param list<mixed> $collectionIds
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function reorder(array $collectionIds, int $userId): array
    {
        $normalizedIds = [];

        foreach ($collectionIds as $collectionId) {
            if ((is_int($collectionId) && $collectionId > 0)
                || (is_string($collectionId) && ctype_digit($collectionId) && (int) $collectionId > 0)) {
                $normalizedIds[] = (int) $collectionId;
                continue;
            }

            return $this->result(EditStatus::INVALID, [
                'collection_ids' => 'The collection order contains an invalid item.',
            ]);
        }

        if (count($normalizedIds) !== count(array_unique($normalizedIds))) {
            return $this->result(EditStatus::INVALID, [
                'collection_ids' => 'Each collection must appear exactly once.',
            ]);
        }

        $storedIds = array_map(
            'intval',
            $this->collections
                ->where('user_id', $userId)
                ->findColumn('id') ?? [],
        );
        $requestedIds = $normalizedIds;
        sort($storedIds, SORT_NUMERIC);
        sort($requestedIds, SORT_NUMERIC);

        if ($storedIds !== $requestedIds) {
            return $this->result(EditStatus::INVALID, [
                'collection_ids' => 'The complete collection order is required.',
            ]);
        }

        if ($normalizedIds === []) {
            return $this->result(EditStatus::UPDATED);
        }

        $rows = array_map(
            static fn (int $id, int $index): array => [
                'id'         => $id,
                'sort_order' => ($index + 1) * 10,
            ],
            $normalizedIds,
            array_keys($normalizedIds),
        );

        if ($this->collections->updateBatch($rows, 'id', count($rows)) === false) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return $this->result(EditStatus::UPDATED);
    }

    /**
     * Validates and updates one collection without changing ownership or order.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function update(int $id, array $input, int $userId): array
    {
        if ($this->findOwnedCollection($id, $userId) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::RULES);

        if (! $validation->run($data)) {
            return $this->result(EditStatus::INVALID, $validation->getErrors());
        }

        try {
            $data['icon'] = $this->svgIconSanitizer->sanitize($data['icon']);
        } catch (InvalidArgumentException $exception) {
            return $this->result(EditStatus::INVALID, [
                'icon' => $exception->getMessage(),
            ]);
        }

        if (! $this->collections->update($id, $data)) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return $this->result(EditStatus::UPDATED);
    }

    /**
     * Deletes one collection using the model's configured delete behavior.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function delete(int $id, int $userId): array
    {
        if ($this->findOwnedCollection($id, $userId) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        if (! $this->collections->delete($id)) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return $this->result(EditStatus::DELETED);
    }

    private function findOwnedCollection(int $id, int $userId): ?array
    {
        $collection = $this->collections->find($id);

        return is_array($collection) && (int) ($collection['user_id'] ?? 0) === $userId
            ? $collection
            : null;
    }

    private function normalizeInput(array $input): array
    {
        $description = $this->stringValue($input, 'description');
        $icon        = $this->stringValue($input, 'icon');

        return [
            'name'        => $this->stringValue($input, 'name'),
            'description' => $description === '' ? null : $description,
            'icon'        => $icon === '' ? null : $icon,
            'is_featured' => $this->booleanValue($input['is_featured'] ?? false),
            'is_hidden'   => $this->booleanValue($input['is_hidden'] ?? false),
        ];
    }

    private function present(array $collection): array
    {
        return [
            'id'          => (int) $collection['id'],
            'name'        => (string) $collection['name'],
            'description' => (string) ($collection['description'] ?? ''),
            'icon'        => (string) ($collection['icon'] ?? ''),
            'is_featured' => (bool) $collection['is_featured'],
            'is_hidden'   => (bool) $collection['is_hidden'],
        ];
    }

    private function stringValue(array $input, string $key): string
    {
        return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
    }

    private function booleanValue(mixed $value): int
    {
        return in_array($value, [true, 1, '1', 'true', 'on'], true) ? 1 : 0;
    }

    /**
     * @param array<string, string> $errors
     *
     * @return array{status: string, errors: array<string, string>}
     */
    private function result(string $status, array $errors = []): array
    {
        return [
            'status' => $status,
            'errors' => $errors,
        ];
    }
}
