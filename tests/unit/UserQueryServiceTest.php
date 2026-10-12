<?php

use App\Services\UserQueryService;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class UserQueryServiceTest extends CIUnitTestCase
{
    public function testAllBuildsTheAdminUserQuery(): void
    {
        $users = $this->getMockBuilder(UserModel::class)
            ->onlyMethods(['withIdentities', 'withGroups', 'findAll'])
            ->addMethods(['orderBy'])
            ->getMock();
        $users->expects($this->once())->method('withIdentities')->willReturnSelf();
        $users->expects($this->once())->method('withGroups')->willReturnSelf();
        $users->expects($this->once())->method('orderBy')->with('username', 'ASC')->willReturnSelf();
        $users->expects($this->once())->method('findAll')->willReturn([]);

        $this->assertSame([], (new UserQueryService($users))->all());
    }
}
