<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Resolves public hub owners from the configured root username or a route.
 */
final class HubOwnerResolver {
    private readonly UserQueryService $users;
    private readonly string $rootUsername;

    public function __construct(
        ?UserQueryService $users = null,
        ?string $rootUsername = null,
    ) {
        $this->users = $users ?? new UserQueryService();
        $this->rootUsername = $rootUsername ?? (string) config('Lincotic')->rootUsername;

        if (preg_match('/\A[a-z0-9]+\z/', $this->rootUsername) !== 1) {
            throw new InvalidArgumentException('The configured root hub username is invalid.');
        }
    }

    public function rootUsername(): string {
        return $this->rootUsername;
    }

    /**
     * Resolves the configured root hub owner when the account is active.
     *
     * @return array{id: int, username: string}|null
     */
    public function root(): ?array {
        return $this->resolve($this->rootUsername);
    }

    /**
     * Resolves an active hub owner by their exact username.
     *
     * @return array{id: int, username: string}|null
     */
    public function resolve(string $username): ?array {
        return $this->users->findActiveByUsername($username);
    }
}
