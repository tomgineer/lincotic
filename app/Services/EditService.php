<?php

namespace App\Services;

use App\Models\CollectionModel;
use App\Models\LinkModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Exceptions\ValidationException;
use CodeIgniter\Shield\Models\UserModel;
use Throwable;

final class EditService
{
    public const string CREATED   = 'created';
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
    ];

    private const array COLLECTION_RULES = [
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

    private ?UserModel $users;

    public function __construct(
        private readonly LinkModel $links = new LinkModel(),
        private readonly CollectionModel $collections = new CollectionModel(),
        ?UserModel $users = null,
    ) {
        $this->users = $users;
    }

    /**
     * Validates and creates a Shield user with the selected role.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function createUser(array $input): array
    {
        $data       = $this->normalizeUserInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules($this->userCreationRules());

        if (! $validation->run($data, null, config('Auth')->DBGroup)) {
            return $this->userResult(self::INVALID, $validation->getErrors());
        }

        $users = $this->userModel();
        $user  = $users->createNewUser([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'active'   => true,
            'status'   => $data['active'] ? null : 'banned',
            'status_message' => $data['active'] ? null : 'Deactivated by an administrator.',
        ]);
        $passwordCheck = service('passwords')->check($data['password'], $user);

        if (! $passwordCheck->isOK()) {
            return $this->userResult(self::INVALID, [
                'password' => $passwordCheck->reason(),
            ]);
        }

        $database = db_connect(config('Auth')->DBGroup);
        $database->transBegin();

        try {
            try {
                if (! $users->save($user)) {
                    $database->transRollback();

                    return $this->userResult(self::INVALID, $users->errors());
                }
            } catch (ValidationException) {
                $database->transRollback();

                return $this->userResult(self::INVALID, $users->errors());
            }

            $createdUser = $users->findById($users->getInsertID());

            if ($createdUser === null) {
                $database->transRollback();

                return $this->userResult(self::INVALID);
            }

            $createdUser->addGroup($data['role']);

            if (! $database->transStatus()) {
                $database->transRollback();

                return $this->userResult(self::INVALID);
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }

        return $this->userResult(self::CREATED);
    }

    /**
     * Returns the users displayed in the admin popover.
     *
     * @return list<array{id: int, username: string, email: string, role: string, active: bool}>
     */
    public function users(): array
    {
        $users = $this->userModel()
            ->withIdentities()
            ->withGroups()
            ->orderBy('username', 'ASC')
            ->findAll();

        return array_map(fn (User $user): array => $this->presentUser($user), $users);
    }

    /**
     * Validates and updates one Shield user.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function updateUser(int $id, array $input, int $actorId): array
    {
        $users = $this->userModel();
        $user = $users
            ->withIdentities()
            ->withGroups()
            ->find($id);

        if (! $user instanceof User) {
            return $this->userResult(self::NOT_FOUND);
        }

        $data       = $this->normalizeUserInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules($this->userUpdateRules($id));

        if (! $validation->run($data, null, config('Auth')->DBGroup)) {
            return $this->userResult(self::INVALID, $validation->getErrors());
        }

        if ($id === $actorId && ! $data['active']) {
            return $this->userResult(self::INVALID, [
                'active' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($id === $actorId && $data['role'] !== 'admin') {
            return $this->userResult(self::INVALID, [
                'role' => 'You cannot remove your own administrator role.',
            ]);
        }

        if ($data['password'] !== '') {
            $passwordCheck = service('passwords')->check($data['password'], $user);

            if (! $passwordCheck->isOK()) {
                return $this->userResult(self::INVALID, [
                    'password' => $passwordCheck->reason(),
                ]);
            }
        }

        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->active = true;
        $user->status = $data['active'] ? null : 'banned';
        $user->status_message = $data['active'] ? null : 'Deactivated by an administrator.';

        if ($data['password'] !== '') {
            $user->password = $data['password'];
        }

        $database = db_connect(config('Auth')->DBGroup);
        $database->transBegin();

        try {
            try {
                if (! $users->save($user)) {
                    $database->transRollback();

                    return $this->userResult(self::INVALID, $users->errors());
                }
            } catch (ValidationException) {
                $database->transRollback();

                return $this->userResult(self::INVALID, $users->errors());
            }

            $groups = $user->getGroups() ?? [];

            if ($groups !== [$data['role']]) {
                if ($groups !== []) {
                    $user->removeGroup(...$groups);
                }

                $user->addGroup($data['role']);
            }

            if (! $database->transStatus()) {
                $database->transRollback();

                return $this->userResult(self::INVALID);
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }

        return $this->userResult(self::UPDATED);
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
     * Returns the fields exposed by the collection editor.
     */
    public function getCollection(int $id): ?array
    {
        $collection = $this->collections->find($id);

        return is_array($collection) ? $this->presentCollection($collection) : null;
    }

    /**
     * Validates and creates one collection for the current user.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function createCollection(array $input, int $userId): array
    {
        $data       = $this->normalizeCollectionInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::COLLECTION_RULES);

        if (! $validation->run($data)) {
            return $this->collectionResult(self::INVALID, $validation->getErrors());
        }

        if ($userId < 1) {
            return $this->collectionResult(self::INVALID);
        }

        $data['user_id'] = $userId;

        if ($this->collections->insert($data) === false) {
            return $this->collectionResult(self::INVALID, $this->collections->errors());
        }

        return $this->collectionResult(self::CREATED);
    }

    /**
     * Validates and updates one collection without changing ownership or order.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function updateCollection(int $id, array $input): array
    {
        if ($this->getCollection($id) === null) {
            return $this->collectionResult(self::NOT_FOUND);
        }

        $data       = $this->normalizeCollectionInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::COLLECTION_RULES);

        if (! $validation->run($data)) {
            return $this->collectionResult(self::INVALID, $validation->getErrors());
        }

        if (! $this->collections->update($id, $data)) {
            return $this->collectionResult(self::INVALID, $this->collections->errors());
        }

        return $this->collectionResult(self::UPDATED);
    }

    /**
     * Deletes one collection using the model's configured delete behavior.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function deleteCollection(int $id): array
    {
        if ($this->getCollection($id) === null) {
            return $this->collectionResult(self::NOT_FOUND);
        }

        if (! $this->collections->delete($id)) {
            return $this->collectionResult(self::INVALID, $this->collections->errors());
        }

        return $this->collectionResult(self::DELETED);
    }

    /**
     * Validates and creates one link for either a collection owner or the current user.
     *
     * @return array{status: string, link: array|null, errors: array<string, string>}
     */
    public function createLink(array $input, int $userId): array
    {
        $prepared = $this->prepareLinkData($input, true);

        if ($prepared['data'] === null) {
            return $this->result(self::INVALID, errors: $prepared['errors']);
        }

        $collection = $prepared['collection'];
        $data       = $prepared['data'];

        if ($data['collection_id'] === null) {
            if ($userId < 1) {
                return $this->result(self::INVALID);
            }

            $data['user_id'] = $userId;
        } elseif (! is_array($collection) || (int) ($collection['user_id'] ?? 0) < 1) {
            return $this->result(self::INVALID, errors: [
                'collection_id' => 'The selected collection is not available.',
            ]);
        } else {
            $data['user_id'] = (int) $collection['user_id'];
        }

        if ($this->links->insert($data) === false) {
            return $this->result(self::INVALID, errors: $this->links->errors());
        }

        return $this->result(self::CREATED);
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

        $prepared = $this->prepareLinkData($input);

        if ($prepared['data'] === null) {
            return $this->result(self::INVALID, errors: $prepared['errors']);
        }

        if (! $this->links->update($id, $prepared['data'])) {
            return $this->result(self::INVALID, errors: $this->links->errors());
        }

        return $this->result(self::UPDATED);
    }

    /**
     * @return array{
     *     data: array<string, int|string|null>|null,
     *     collection: array|null,
     *     errors: array<string, string>
     * }
     */
    private function prepareLinkData(array $input, bool $allowUnassigned = false): array
    {
        $data       = $this->normalizeLinkInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules(self::LINK_RULES);

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

    private function normalizeCollectionInput(array $input): array
    {
        $description = $this->stringValue($input, 'description');
        $icon = $this->stringValue($input, 'icon');

        return [
            'name'        => $this->stringValue($input, 'name'),
            'slug'        => strtolower($this->stringValue($input, 'slug')),
            'description' => $description === '' ? null : $description,
            'icon'        => $icon === '' ? null : $icon,
            'is_featured' => $this->booleanValue($input['is_featured'] ?? false),
        ];
    }

    private function normalizeUserInput(array $input): array
    {
        return [
            'username' => $this->stringValue($input, 'username'),
            'email'    => strtolower($this->stringValue($input, 'email')),
            'password' => is_string($input['password'] ?? null) ? $input['password'] : '',
            'role'     => $this->stringValue($input, 'role'),
            'active'   => $this->booleanValue($input['active'] ?? false),
        ];
    }

    private function userCreationRules(): array
    {
        $config        = config('Auth');
        $usernameRules = $config->usernameValidationRules;
        $emailRules    = $config->emailValidationRules;

        $usernameRules['rules'][] = sprintf('is_unique[%s.username]', $config->tables['users']);
        $emailRules['rules'][] = sprintf('is_unique[%s.secret]', $config->tables['identities']);

        return [
            'username' => $usernameRules,
            'email'    => $emailRules,
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => ['required', Passwords::getMaxLengthRule()],
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes',
                ],
            ],
            'role' => [
                'label' => 'Role',
                'rules' => 'required|in_list[user,admin]',
            ],
            'active' => [
                'label' => 'Active account',
                'rules' => 'required|in_list[0,1]',
            ],
        ];
    }

    private function userUpdateRules(int $id): array
    {
        $config        = config('Auth');
        $usernameRules = $config->usernameValidationRules;
        $emailRules    = $config->emailValidationRules;

        $usernameRules['rules'][] = sprintf('is_unique[%s.username,id,%d]', $config->tables['users'], $id);
        $emailRules['rules'][] = sprintf('is_unique[%s.secret,user_id,%d]', $config->tables['identities'], $id);

        return [
            'username' => $usernameRules,
            'email'    => $emailRules,
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => ['permit_empty', Passwords::getMaxLengthRule()],
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes',
                ],
            ],
            'role' => [
                'label' => 'Role',
                'rules' => 'required|in_list[user,admin]',
            ],
            'active' => [
                'label' => 'Active account',
                'rules' => 'required|in_list[0,1]',
            ],
        ];
    }

    private function userModel(): UserModel
    {
        if ($this->users === null) {
            $provider = model(setting('Auth.userProvider'));
            assert($provider instanceof UserModel);
            $this->users = $provider;
        }

        return $this->users;
    }

    /**
     * @return array{id: int, username: string, email: string, role: string, active: bool}
     */
    private function presentUser(User $user): array
    {
        $groups = $user->getGroups() ?? [];

        return [
            'id'       => (int) $user->id,
            'username' => (string) ($user->username ?? ''),
            'email'    => (string) ($user->email ?? ''),
            'role'     => (string) ($groups[0] ?? config('AuthGroups')->defaultGroup),
            'active'   => ! $user->isBanned(),
        ];
    }

    private function presentLink(array $link): array
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
        ];
    }

    private function presentCollection(array $collection): array
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

    /**
     * @param array<string, string> $errors
     *
     * @return array{status: string, errors: array<string, string>}
     */
    private function collectionResult(string $status, array $errors = []): array
    {
        return [
            'status' => $status,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, string> $errors
     *
     * @return array{status: string, errors: array<string, string>}
     */
    private function userResult(string $status, array $errors = []): array
    {
        return [
            'status' => $status,
            'errors' => $errors,
        ];
    }
}
