<?php

use App\Services\UserQueryService;
use CodeIgniter\Shield\Entities\User;
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

    public function testFindByUsernameReturnsTheHubOwner(): void
    {
        $user = new User([
            'id'       => 2,
            'username' => 'civic',
        ]);
        $users = $this->getMockBuilder(UserModel::class)
            ->onlyMethods(['first'])
            ->addMethods(['where'])
            ->getMock();
        $users->expects($this->once())
            ->method('where')
            ->with('username', 'civic')
            ->willReturnSelf();
        $users->expects($this->once())->method('first')->willReturn($user);

        $this->assertSame(
            ['id' => 2, 'username' => 'civic'],
            (new UserQueryService($users))->findByUsername('civic'),
        );
    }

    public function testFindByUsernameReturnsNullForAnUnknownUser(): void
    {
        $users = $this->getMockBuilder(UserModel::class)
            ->onlyMethods(['first'])
            ->addMethods(['where'])
            ->getMock();
        $users->expects($this->once())->method('where')->willReturnSelf();
        $users->expects($this->once())->method('first')->willReturn(null);

        $this->assertNull((new UserQueryService($users))->findByUsername('missing-user'));
    }
}
