<?php

use App\Services\HubOwnerResolver;
use App\Services\UserQueryService;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HubOwnerResolverTest extends CIUnitTestCase
{
    public function testRootUsesTheConfiguredUsername(): void
    {
        $user = new User(['id' => 4, 'username' => 'main']);
        $users = $this->getMockBuilder(UserModel::class)
            ->onlyMethods(['first'])
            ->addMethods(['where'])
            ->getMock();
        $users->expects($this->once())->method('where')->with('username', 'main')->willReturnSelf();
        $users->expects($this->once())->method('first')->willReturn($user);

        $resolver = new HubOwnerResolver(new UserQueryService($users), 'main');

        $this->assertSame('main', $resolver->rootUsername());
        $this->assertSame(['id' => 4, 'username' => 'main'], $resolver->root());
    }

    public function testResolveDoesNotExposeInactiveUsers(): void
    {
        $user = new User(['id' => 7, 'username' => 'tom', 'status' => 'banned']);
        $users = $this->getMockBuilder(UserModel::class)
            ->onlyMethods(['first'])
            ->addMethods(['where'])
            ->getMock();
        $users->expects($this->once())->method('where')->with('username', 'tom')->willReturnSelf();
        $users->expects($this->once())->method('first')->willReturn($user);

        $resolver = new HubOwnerResolver(new UserQueryService($users), 'main');

        $this->assertNull($resolver->resolve('tom'));
    }
}
