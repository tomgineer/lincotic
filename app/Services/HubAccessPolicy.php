<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;

/**
 * Centralizes viewer permissions for user-owned hubs and account management.
 */
final class HubAccessPolicy {

    /**
     * Allows hidden hub content to be viewed only by its active owner.
     */
    public function canViewHidden(?User $viewer, int $ownerId): bool {
        return $this->isOwner($viewer, $ownerId);
    }

    /**
     * Allows a library to be edited only by its active owner.
     */
    public function canEditLibrary(?User $viewer, int $ownerId): bool {
        return $this->isOwner($viewer, $ownerId);
    }

    /**
     * Allows user administration only to active members of the admin group.
     */
    public function canManageUsers(?User $viewer): bool {
        return $viewer !== null
            && ! $viewer->isBanned()
            && $viewer->inGroup('admin');
    }

    /**
     * Confirms that the viewer is active and owns the requested hub.
     */
    public function isOwner(?User $viewer, int $ownerId): bool {
        return $viewer !== null
            && ! $viewer->isBanned()
            && (int) ($viewer->id ?? 0) === $ownerId;
    }
}
