<?php

namespace App\Services\Editing;

use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Exceptions\ValidationException;
use CodeIgniter\Shield\Models\UserModel;
use Throwable;

final class UserEditor {

    private ?UserModel $users;

    public function __construct(?UserModel $users = null) {
        $this->users = $users;
    }

    /**
     * Validates and creates a Shield user with the selected role.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function create(array $input): array {
        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules($this->creationRules());

        if (! $validation->run($data, null, config('Auth')->DBGroup)) {
            return $this->result(EditStatus::INVALID, $validation->getErrors());
        }

        $users = $this->userModel();
        $user  = $users->createNewUser([
            'username'       => $data['username'],
            'email'          => $data['email'],
            'password'       => $data['password'],
            'active'         => true,
            'status'         => $data['active'] ? null : 'banned',
            'status_message' => $data['active'] ? null : 'Deactivated by an administrator.',
        ]);
        $passwordCheck = service('passwords')->check($data['password'], $user);

        if (! $passwordCheck->isOK()) {
            return $this->result(EditStatus::INVALID, [
                'password' => $passwordCheck->reason(),
            ]);
        }

        $database = db_connect(config('Auth')->DBGroup);
        $database->transBegin();

        try {
            try {
                if (! $users->save($user)) {
                    $database->transRollback();

                    return $this->result(EditStatus::INVALID, $users->errors());
                }
            } catch (ValidationException) {
                $database->transRollback();

                return $this->result(EditStatus::INVALID, $users->errors());
            }

            $createdUser = $users->findById($users->getInsertID());

            if ($createdUser === null) {
                $database->transRollback();

                return $this->result(EditStatus::INVALID);
            }

            $createdUser->addGroup($data['role']);

            if (! $database->transStatus()) {
                $database->transRollback();

                return $this->result(EditStatus::INVALID);
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }

        return $this->result(EditStatus::CREATED);
    }

    /**
     * Validates and updates one Shield user.
     *
     * @return array{status: string, errors: array<string, string>}
     */
    public function update(int $id, array $input, int $actorId): array {
        $users = $this->userModel();
        $user  = $users
            ->withIdentities()
            ->withGroups()
            ->find($id);

        if (! $user instanceof User) {
            return $this->result(EditStatus::NOT_FOUND);
        }

        $data       = $this->normalizeInput($input);
        $validation = service('validation');
        $validation->reset();
        $validation->setRules($this->updateRules($id));

        if (! $validation->run($data, null, config('Auth')->DBGroup)) {
            return $this->result(EditStatus::INVALID, $validation->getErrors());
        }

        if ($id === $actorId && ! $data['active']) {
            return $this->result(EditStatus::INVALID, [
                'active' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($id === $actorId && $data['role'] !== 'admin') {
            return $this->result(EditStatus::INVALID, [
                'role' => 'You cannot remove your own administrator role.',
            ]);
        }

        if ($data['password'] !== '') {
            $passwordCheck = service('passwords')->check($data['password'], $user);

            if (! $passwordCheck->isOK()) {
                return $this->result(EditStatus::INVALID, [
                    'password' => $passwordCheck->reason(),
                ]);
            }
        }

        $user->username       = $data['username'];
        $user->email          = $data['email'];
        $user->active         = true;
        $user->status         = $data['active'] ? null : 'banned';
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

                    return $this->result(EditStatus::INVALID, $users->errors());
                }
            } catch (ValidationException) {
                $database->transRollback();

                return $this->result(EditStatus::INVALID, $users->errors());
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

                return $this->result(EditStatus::INVALID);
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }

        return $this->result(EditStatus::UPDATED);
    }

    private function normalizeInput(array $input): array {
        return [
            'username' => $this->stringValue($input, 'username'),
            'email'    => strtolower($this->stringValue($input, 'email')),
            'password' => is_string($input['password'] ?? null) ? $input['password'] : '',
            'role'     => $this->stringValue($input, 'role'),
            'active'   => $this->booleanValue($input['active'] ?? false),
        ];
    }

    /**
     * Extends Shield's user rules with creation-time uniqueness constraints.
     */
    private function creationRules(): array {
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

    /**
     * Extends Shield's user rules while excluding the edited user from uniqueness checks.
     */
    private function updateRules(int $id): array {
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

    /**
     * Lazily resolves the configured Shield user provider.
     */
    private function userModel(): UserModel {
        if ($this->users === null) {
            $provider = model(setting('Auth.userProvider'));
            assert($provider instanceof UserModel);
            $this->users = $provider;
        }

        return $this->users;
    }

    private function stringValue(array $input, string $key): string {
        return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
    }

    private function booleanValue(mixed $value): int {
        return in_array($value, [true, 1, '1', 'true', 'on'], true) ? 1 : 0;
    }

    /**
     * Builds the shared response envelope returned by user mutations.
     *
     * @param array<string, string> $errors
     *
     * @return array{status: string, errors: array<string, string>}
     */
    private function result(string $status, array $errors = []): array {
        return [
            'status' => $status,
            'errors' => $errors,
        ];
    }
}
