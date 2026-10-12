<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\LinkModel;

final class EditService
{
    public const string UPDATED   = 'updated';
    public const string DELETED   = 'deleted';
    public const string INVALID   = 'invalid';
    public const string NOT_FOUND = 'not_found';

    private const array LINK_RULES = [
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
            'rules' => 'required|is_natural_no_zero',
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
    ];

    public function __construct(
        private readonly LinkModel $links = new LinkModel(),
        private readonly CollectionModel $collections = new CollectionModel(),
    ) {
    }

    /**
     * Returns the fields exposed by the link editor.
     */
    public function getLink(int $id): ?array
    {
        $link = $this->links->find($id);

        return is_array($link) ? $this->presentLink($link) : null;
    }

    /**
     * Validates and updates one link without changing ownership or activity data.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function updateLink(int $id, array $input): array
    {
        if ($this->getLink($id) === null) {
            return $this->result(self::NOT_FOUND);
        }

        $data       = $this->normalizeLinkInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::LINK_RULES);

        if (! $validation->run($data)) {
            return $this->result(self::INVALID, errors: $validation->getErrors());
        }

        $collectionId = (int) $data['collection_id'];

        if ($this->collections->find($collectionId) === null) {
            return $this->result(self::INVALID, errors: [
                'collection_id' => 'The selected collection is not available.',
            ]);
        }

        $data['collection_id'] = $collectionId;

        if (! $this->links->update($id, $data)) {
            return $this->result(self::INVALID, errors: $this->links->errors());
        }

        return $this->result(self::UPDATED);
    }

    /**
     * Soft deletes one link through the model's soft-delete configuration.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function deleteLink(int $id): array
    {
        if ($this->getLink($id) === null) {
            return $this->result(self::NOT_FOUND);
        }

        if (! $this->links->delete($id)) {
            return $this->result(self::INVALID, errors: $this->links->errors());
        }

        return $this->result(self::DELETED);
    }

    private function normalizeLinkInput(array $input): array
    {
        $importance = $this->stringValue($input, 'importance');
        $description = $this->stringValue($input, 'description');

        return [
            'title'             => $this->stringValue($input, 'title'),
            'url'               => $this->stringValue($input, 'url'),
            'collection_id'     => $this->integerValue($input['collection_id'] ?? null),
            'description'       => $description === '' ? null : $description,
            'importance'        => $importance === '' ? null : $importance,
            'is_favorite'       => $this->booleanValue($input['is_favorite'] ?? false),
            'is_pending_review' => $this->booleanValue($input['is_pending_review'] ?? false),
        ];
    }

    private function presentLink(array $link): array
    {
        return [
            'id'                => (int) $link['id'],
            'collection_id'     => (int) $link['collection_id'],
            'title'             => (string) $link['title'],
            'url'               => (string) $link['url'],
            'description'       => (string) ($link['description'] ?? ''),
            'importance'        => (string) ($link['importance'] ?? ''),
            'is_favorite'       => (bool) $link['is_favorite'],
            'is_pending_review' => (bool) $link['is_pending_review'],
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
