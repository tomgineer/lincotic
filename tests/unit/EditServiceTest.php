<?php

use App\Models\CollectionModel;
use App\Models\LinkModel;
use App\Services\EditService;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EditServiceTest extends CIUnitTestCase
{
    public function testCreateUserRejectsInvalidInputBeforeUsingShieldModel(): void
    {
        $users = $this->createMock(UserModel::class);
        $users->expects($this->never())->method('createNewUser');
        $users->expects($this->never())->method('save');

        $service = new EditService(
            $this->createMock(LinkModel::class),
            $this->createMock(CollectionModel::class),
            $users,
        );
        $result = $service->createUser([
            'username' => '',
            'email'    => 'not-an-email',
            'password' => '',
            'role'     => 'owner',
        ]);

        $this->assertSame(EditService::INVALID, $result['status']);
        $this->assertArrayHasKey('username', $result['errors']);
        $this->assertArrayHasKey('email', $result['errors']);
        $this->assertArrayHasKey('password', $result['errors']);
        $this->assertArrayHasKey('role', $result['errors']);
    }

    public function testGetLinkReturnsOnlyNormalizedEditorFields(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(12)->willReturn([
            'id'                => '12',
            'user_id'           => '4',
            'collection_id'     => '7',
            'title'             => 'Example',
            'url'               => 'https://example.com',
            'description'       => null,
            'importance'        => null,
            'is_favorite'       => '1',
            'is_pending_review' => '0',
            'click_count'       => '99',
        ]);

        $service = new EditService($links, $this->createMock(CollectionModel::class));

        $this->assertSame([
            'id'                => 12,
            'collection_id'     => 7,
            'title'             => 'Example',
            'url'               => 'https://example.com',
            'description'       => '',
            'importance'        => '',
            'is_favorite'       => true,
            'is_pending_review' => false,
        ], $service->getLink(12));
    }

    public function testGetCollectionReturnsOnlyNormalizedEditorFields(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn([
            'id'          => '8',
            'user_id'     => '4',
            'name'        => 'Work',
            'slug'        => 'work',
            'description' => null,
            'icon'        => null,
            'is_featured' => '1',
            'sort_order'  => '20',
        ]);

        $service = new EditService($this->createMock(LinkModel::class), $collections);

        $this->assertSame([
            'id'          => 8,
            'name'        => 'Work',
            'slug'        => 'work',
            'description' => '',
            'icon'        => '',
            'is_featured' => true,
        ], $service->getCollection(8));
    }

    public function testUpdateCollectionRejectsInvalidInput(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn($this->storedCollection());
        $collections->expects($this->never())->method('update');

        $service = new EditService($this->createMock(LinkModel::class), $collections);
        $result  = $service->updateCollection(8, [
            'name' => '',
            'slug' => 'Not a slug',
        ]);

        $this->assertSame(EditService::INVALID, $result['status']);
        $this->assertArrayHasKey('name', $result['errors']);
        $this->assertArrayHasKey('slug', $result['errors']);
    }

    public function testUpdateCollectionNormalizesAndPersistsEditorInput(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('find')->with(8)->willReturn($this->storedCollection());
        $collections
            ->expects($this->once())
            ->method('update')
            ->with(8, [
                'name'        => 'Updated collection',
                'slug'        => 'updated-collection',
                'description' => 'Useful links',
                'icon'        => null,
                'is_featured' => 1,
            ])
            ->willReturn(true);

        $service = new EditService($this->createMock(LinkModel::class), $collections);
        $result  = $service->updateCollection(8, [
            'name'        => '  Updated collection  ',
            'slug'        => '  UPDATED-COLLECTION  ',
            'description' => '  Useful links  ',
            'icon'        => '',
            'is_featured' => true,
        ]);

        $this->assertSame(EditService::UPDATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteCollectionUsesTheConfiguredModelDelete(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('find')->with(8)->willReturn($this->storedCollection());
        $collections->expects($this->once())->method('delete')->with(8)->willReturn(true);

        $service = new EditService($this->createMock(LinkModel::class), $collections);
        $result  = $service->deleteCollection(8);

        $this->assertSame(EditService::DELETED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testCreateRejectsInvalidEditorInput(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->expects($this->never())->method('insert');

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new EditService($links, $collections);
        $result  = $service->createLink([
            'title'         => '',
            'url'           => 'not-a-url',
            'collection_id' => '0',
        ], 4);

        $this->assertSame(EditService::INVALID, $result['status']);
        $this->assertArrayHasKey('title', $result['errors']);
        $this->assertArrayHasKey('url', $result['errors']);
        $this->assertArrayHasKey('collection_id', $result['errors']);
    }

    public function testCreateUsesTheSelectedCollectionOwner(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links
            ->expects($this->once())
            ->method('insert')
            ->with([
                'title'             => 'New link',
                'url'               => 'https://example.com/new',
                'collection_id'     => 8,
                'description'       => null,
                'importance'        => 'high',
                'is_favorite'       => 0,
                'is_pending_review' => 1,
                'user_id'           => 4,
            ])
            ->willReturn(27);

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('find')->with(8)->willReturn([
            'id'      => 8,
            'user_id' => 4,
        ]);

        $service = new EditService($links, $collections);
        $result  = $service->createLink([
            'title'             => '  New link  ',
            'url'               => ' https://example.com/new ',
            'collection_id'     => '8',
            'description'       => '',
            'importance'        => 'high',
            'is_favorite'       => false,
            'is_pending_review' => true,
        ], 99);

        $this->assertSame(EditService::CREATED, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testCreateAllowsAnUnassignedLinkForTheCurrentUser(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links
            ->expects($this->once())
            ->method('insert')
            ->with([
                'title'             => 'Inbox link',
                'url'               => 'https://example.com/inbox',
                'collection_id'     => null,
                'description'       => null,
                'importance'        => null,
                'is_favorite'       => 0,
                'is_pending_review' => 0,
                'user_id'           => 9,
            ])
            ->willReturn(28);

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new EditService($links, $collections);
        $result  = $service->createLink([
            'title'         => 'Inbox link',
            'url'           => 'https://example.com/inbox',
            'collection_id' => '',
        ], 9);

        $this->assertSame(EditService::CREATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testUpdateReturnsNotFoundForMissingLink(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(404)->willReturn(null);
        $links->expects($this->never())->method('update');

        $service = new EditService($links, $this->createMock(CollectionModel::class));
        $result  = $service->updateLink(404, []);

        $this->assertSame(EditService::NOT_FOUND, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testUpdateRejectsInvalidEditorInput(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(3)->willReturn($this->storedLink());
        $links->expects($this->never())->method('update');

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new EditService($links, $collections);
        $result  = $service->updateLink(3, [
            'title'         => '',
            'url'           => 'not-a-url',
            'collection_id' => '0',
            'importance'    => 'urgent',
        ]);

        $this->assertSame(EditService::INVALID, $result['status']);
        $this->assertArrayHasKey('title', $result['errors']);
        $this->assertArrayHasKey('url', $result['errors']);
        $this->assertArrayHasKey('collection_id', $result['errors']);
        $this->assertArrayHasKey('importance', $result['errors']);
    }

    public function testUpdateRequiresAnAssignedCollection(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(3)->willReturn($this->storedLink());
        $links->expects($this->never())->method('update');

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new EditService($links, $collections);
        $result  = $service->updateLink(3, [
            'title'         => 'Existing title',
            'url'           => 'https://example.com',
            'collection_id' => '',
        ]);

        $this->assertSame(EditService::INVALID, $result['status']);
        $this->assertSame('Select a collection.', $result['errors']['collection_id']);
    }

    public function testUpdateNormalizesAndPersistsEditorInput(): void
    {
        $stored  = $this->storedLink();
        $links = $this->createMock(LinkModel::class);
        $links
            ->expects($this->once())
            ->method('find')
            ->with(3)
            ->willReturn($stored);
        $links
            ->expects($this->once())
            ->method('update')
            ->with(3, [
                'title'             => 'Updated title',
                'url'               => 'https://example.com/path',
                'collection_id'     => 8,
                'description'       => 'Useful note',
                'importance'        => null,
                'is_favorite'       => 1,
                'is_pending_review' => 0,
            ])
            ->willReturn(true);

        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn(['id' => 8]);

        $service = new EditService($links, $collections);
        $result  = $service->updateLink(3, [
            'title'             => '  Updated title  ',
            'url'               => ' https://example.com/path ',
            'collection_id'     => '8',
            'description'       => '  Useful note  ',
            'importance'        => '',
            'is_favorite'       => true,
            'is_pending_review' => false,
        ]);

        $this->assertSame(EditService::UPDATED, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteReturnsNotFoundForMissingLink(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(404)->willReturn(null);
        $links->expects($this->never())->method('delete');

        $service = new EditService($links, $this->createMock(CollectionModel::class));
        $result  = $service->deleteLink(404);

        $this->assertSame(EditService::NOT_FOUND, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteUsesTheSoftDeletingLinkModel(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->expects($this->once())->method('find')->with(3)->willReturn($this->storedLink());
        $links->expects($this->once())->method('delete')->with(3)->willReturn(true);

        $service = new EditService($links, $this->createMock(CollectionModel::class));
        $result  = $service->deleteLink(3);

        $this->assertSame(EditService::DELETED, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    private function storedLink(): array
    {
        return [
            'id'                => '3',
            'collection_id'     => '5',
            'title'             => 'Existing title',
            'url'               => 'https://example.com',
            'description'       => null,
            'importance'        => 'low',
            'is_favorite'       => '0',
            'is_pending_review' => '1',
        ];
    }

    private function storedCollection(): array
    {
        return [
            'id'          => '8',
            'user_id'     => '4',
            'name'        => 'Work',
            'slug'        => 'work',
            'description' => null,
            'icon'        => null,
            'is_featured' => '0',
            'sort_order'  => '20',
        ];
    }
}
