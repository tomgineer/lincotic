<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

final class UserQueryService
{
    private ?UserModel $users;

    public function __construct(?UserModel $users = null)
    {
        $this->users = $users;
    }

    /**
     * Returns the users displayed in the admin popover.
     *
     * @return list<array{id: int, username: string, email: string, role: string, active: bool}>
     */
    public function all(): array
    {
        $users = $this->userModel()
            ->withIdentities()
            ->withGroups()
            ->orderBy('username', 'ASC')
            ->findAll();

        return array_map(fn (User $user): array => $this->present($user), $users);
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
    private function present(User $user): array
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
}
