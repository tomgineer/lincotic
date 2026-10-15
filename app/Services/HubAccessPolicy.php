<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;

/**
 * Centralizes viewer permissions for user-owned hubs and account management.
 */
final class HubAccessPolicy
{
    public function canViewHidden(?User $viewer, int $ownerId): bool
    {
        return $this->isOwner($viewer, $ownerId);
    }

    public function canEditLibrary(?User $viewer, int $ownerId): bool
    {
        return $this->isOwner($viewer, $ownerId);
    }

    public function canManageUsers(?User $viewer): bool
    {
        return $viewer !== null
            && ! $viewer->isBanned()
            && $viewer->inGroup('admin');
    }

    public function isOwner(?User $viewer, int $ownerId): bool
    {
        return $viewer !== null
            && ! $viewer->isBanned()
            && (int) ($viewer->id ?? 0) === $ownerId;
    }
}
