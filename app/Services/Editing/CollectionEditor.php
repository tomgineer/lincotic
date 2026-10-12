<?php

namespace App\Services\Editing;

use App\Models\CollectionModel;

final class CollectionEditor
{
    private const array RULES = [
        'name' => [
            'label' => 'Name',
            'rules' => 'required|max_length[120]',
        ],
        'slug' => [
            'label' => 'Slug',
            'rules' => 'required|max_length[150]|regex_match[/^[a-z0-9]+(?:-[a-z0-9]+)*$/]',
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
    ];

    public function __construct(
        private readonly CollectionModel $collections = new CollectionModel(),
    ) {
    }

    /**
     * Returns the fields exposed by the collection editor.
     */
    public function get(int $id): ?array
    {
        $collection = $this->collections->find($id);

        return is_array($collection) ? $this->present($collection) : null;
    }

    /**
     * Validates and creates one collection for the current user.
     *
     * @return array{status: string, errors: array<string, string>}
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

        if ($userId < 1) {
            return $this->result(EditStatus::INVALID);
        }

        $data['user_id'] = $userId;

        if ($this->collections->insert($data) === false) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return $this->result(EditStatus::CREATED);
    }

    /**
     * Validates and updates one collection without changing ownership or order.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function update(int $id, array $input): array
    {
        if ($this->get($id) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::RULES);

        if (! $validation->run($data)) {
            return $this->result(EditStatus::INVALID, $validation->getErrors());
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
    public function delete(int $id): array
    {
        if ($this->get($id) === null) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        if (! $this->collections->delete($id)) {
            return $this->result(EditStatus::INVALID, $this->collections->errors());
        }

        return $this->result(EditStatus::DELETED);
    }

    private function normalizeInput(array $input): array
    {
        $description = $this->stringValue($input, 'description');
        $icon        = $this->stringValue($input, 'icon');

        return [
            'name'        => $this->stringValue($input, 'name'),
            'slug'        => strtolower($this->stringValue($input, 'slug')),
            'description' => $description === '' ? null : $description,
            'icon'        => $icon === '' ? null : $icon,
            'is_featured' => $this->booleanValue($input['is_featured'] ?? false),
        ];
    }

    private function present(array $collection): array
    {
        return [
            'id'          => (int) $collection['id'],
            'name'        => (string) $collection['name'],
            'slug'        => (string) $collection['slug'],
            'description' => (string) ($collection['description'] ?? ''),
            'icon'        => (string) ($collection['icon'] ?? ''),
            'is_featured' => (bool) $collection['is_featured'],
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
