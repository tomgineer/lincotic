<?php

use App\Services\HubAccessPolicy;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HubAccessPolicyTest extends CIUnitTestCase
{
    public function testOwnerCanViewAndEditTheirOwnLibraryRegardlessOfRole(): void
    {
        $viewer = new User(['id' => 7, 'username' => 'tom']);
        $viewer->setGroupsCache(['user']);
        $policy = new HubAccessPolicy();

        $this->assertTrue($policy->canViewHidden($viewer, 7));
        $this->assertTrue($policy->canEditLibrary($viewer, 7));
        $this->assertFalse($policy->canManageUsers($viewer));
    }

    public function testAdminCannotViewOrEditAnotherUsersLibrary(): void
    {
        $viewer = new User(['id' => 1, 'username' => 'admin']);
        $viewer->setGroupsCache(['admin']);
        $policy = new HubAccessPolicy();

        $this->assertFalse($policy->canViewHidden($viewer, 7));
        $this->assertFalse($policy->canEditLibrary($viewer, 7));
        $this->assertTrue($policy->canManageUsers($viewer));
    }

    public function testGuestCannotAccessPrivateHubCapabilities(): void
    {
        $policy = new HubAccessPolicy();

        $this->assertFalse($policy->canViewHidden(null, 7));
        $this->assertFalse($policy->canEditLibrary(null, 7));
        $this->assertFalse($policy->canManageUsers(null));
    }

    public function testBannedOwnerHasNoHubCapabilities(): void
    {
        $viewer = new User(['id' => 7, 'username' => 'tom', 'status' => 'banned']);
        $viewer->setGroupsCache(['admin']);
        $policy = new HubAccessPolicy();

        $this->assertFalse($policy->canViewHidden($viewer, 7));
        $this->assertFalse($policy->canEditLibrary($viewer, 7));
        $this->assertFalse($policy->canManageUsers($viewer));
    }
}
