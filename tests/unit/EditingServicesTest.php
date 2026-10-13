<?php

use App\Models\CollectionModel;
use App\Models\LinkModel;
use App\Services\Editing\CollectionEditor;
use App\Services\Editing\EditStatus;
use App\Services\Editing\LinkEditor;
use App\Services\Editing\UserEditor;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EditingServicesTest extends CIUnitTestCase
{
    public function testCreateUserRejectsInvalidInputBeforeUsingShieldModel(): void
    {
        $users = $this->createMock(UserModel::class);
        $users->expects($this->never())->method('createNewUser');
        $users->expects($this->never())->method('save');

        $service = new UserEditor($users);
        $result = $service->create([
            'username' => '',
            'email'    => 'not-an-email',
            'password' => '',
            'role'     => 'owner',
        ]);

        $this->assertSame(EditStatus::INVALID, $result['status']);
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
            'is_hidden'         => '1',
            'click_count'       => '99',
        ]);

        $service = new LinkEditor($links, $this->createMock(CollectionModel::class));

        $this->assertSame([
            'id'                => 12,
            'collection_id'     => 7,
            'title'             => 'Example',
            'url'               => 'https://example.com',
            'description'       => '',
            'importance'        => '',
            'is_favorite'       => true,
            'is_pending_review' => false,
            'is_hidden'         => true,
        ], $service->get(12));
    }

    public function testGetCollectionReturnsOnlyNormalizedEditorFields(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn([
            'id'          => '8',
            'user_id'     => '4',
            'name'        => 'Work',
            'description' => null,
            'icon'        => null,
            'is_featured' => '1',
            'is_hidden'   => '1',
            'sort_order'  => '20',
        ]);

        $service = new CollectionEditor($collections);

        $this->assertSame([
            'id'          => 8,
            'name'        => 'Work',
            'description' => '',
            'icon'        => '',
            'is_featured' => true,
            'is_hidden'   => true,
        ], $service->get(8));
    }

    public function testCreateCollectionRejectsInvalidInput(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('insert');

        $service = new CollectionEditor($collections);
        $result  = $service->create([
            'name' => '',
        ], 4);

        $this->assertSame(EditStatus::INVALID, $result['status']);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    public function testCreateCollectionNormalizesAndPersistsInputForCurrentUser(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections
            ->expects($this->once())
            ->method('insert')
            ->with([
                'name'        => 'New collection',
                'description' => 'Useful links',
                'icon'        => null,
                'is_featured' => 1,
                'is_hidden'   => 1,
                'user_id'     => 4,
            ])
            ->willReturn(15);

        $service = new CollectionEditor($collections);
        $result  = $service->create([
            'name'        => '  New collection  ',
            'description' => '  Useful links  ',
            'icon'        => '',
            'is_featured' => true,
            'is_hidden'   => true,
        ], 4);

        $this->assertSame(EditStatus::CREATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testCreateCollectionSanitizesSvgIconBeforePersisting(): void
    {
        $persisted = null;
        $collections = $this->createMock(CollectionModel::class);
        $collections
            ->expects($this->once())
            ->method('insert')
            ->willReturnCallback(static function (array $data) use (&$persisted): int {
                $persisted = $data;

                return 15;
            });

        $result = (new CollectionEditor($collections))->create([
            'name' => 'Infrastructure',
            'icon' => '<svg width="24" height="24" class="icon icon-tabler" onload="alert(1)"><path d="M3 18h18" /></svg>',
        ], 4);

        $this->assertSame(EditStatus::CREATED, $result['status']);
        $this->assertIsArray($persisted);
        $this->assertStringContainsString('viewBox="0 0 24 24"', $persisted['icon']);
        $this->assertStringContainsString('d="M3 18h18"', $persisted['icon']);
        $this->assertStringNotContainsString('width="24"', $persisted['icon']);
        $this->assertStringNotContainsString('height="24"', $persisted['icon']);
        $this->assertStringNotContainsString('class=', $persisted['icon']);
        $this->assertStringNotContainsString('onload', $persisted['icon']);
    }

    public function testCreateCollectionRejectsInvalidSvgIcon(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('insert');

        $result = (new CollectionEditor($collections))->create([
            'name' => 'Infrastructure',
            'icon' => '<svg><script>alert(1)</script></svg>',
        ], 4);

        $this->assertSame(EditStatus::INVALID, $result['status']);
        $this->assertArrayHasKey('icon', $result['errors']);
    }

    public function testReorderCollectionsRejectsDuplicateIds(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('findColumn');
        $collections->expects($this->never())->method('updateBatch');

        $result = (new CollectionEditor($collections))->reorder([5, 5]);

        $this->assertSame(EditStatus::INVALID, $result['status']);
        $this->assertArrayHasKey('collection_ids', $result['errors']);
    }

    public function testReorderCollectionsPersistsEveryPositionInOneBatch(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('findColumn')->with('id')->willReturn(['5', '8', '13']);
        $collections
            ->expects($this->once())
            ->method('updateBatch')
            ->with([
                ['id' => 13, 'sort_order' => 10],
                ['id' => 5, 'sort_order' => 20],
                ['id' => 8, 'sort_order' => 30],
            ], 'id', 3)
            ->willReturn(3);

        $result = (new CollectionEditor($collections))->reorder(['13', '5', '8']);

        $this->assertSame(EditStatus::UPDATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testUpdateCollectionRejectsInvalidInput(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn($this->storedCollection());
        $collections->expects($this->never())->method('update');

        $service = new CollectionEditor($collections);
        $result  = $service->update(8, [
            'name' => '',
        ]);

        $this->assertSame(EditStatus::INVALID, $result['status']);
        $this->assertArrayHasKey('name', $result['errors']);
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
                'description' => 'Useful links',
                'icon'        => null,
                'is_featured' => 1,
                'is_hidden'   => 1,
            ])
            ->willReturn(true);

        $service = new CollectionEditor($collections);
        $result  = $service->update(8, [
            'name'        => '  Updated collection  ',
            'description' => '  Useful links  ',
            'icon'        => '',
            'is_featured' => true,
            'is_hidden'   => true,
        ]);

        $this->assertSame(EditStatus::UPDATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteCollectionUsesTheConfiguredModelDelete(): void
    {
        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('find')->with(8)->willReturn($this->storedCollection());
        $collections->expects($this->once())->method('delete')->with(8)->willReturn(true);

        $service = new CollectionEditor($collections);
        $result  = $service->delete(8);

        $this->assertSame(EditStatus::DELETED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testCreateRejectsInvalidEditorInput(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->expects($this->never())->method('insert');

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new LinkEditor($links, $collections);
        $result  = $service->create([
            'title'         => '',
            'url'           => 'not-a-url',
            'collection_id' => '0',
        ], 4);

        $this->assertSame(EditStatus::INVALID, $result['status']);
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
                'is_hidden'         => 1,
                'user_id'           => 4,
            ])
            ->willReturn(27);

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->once())->method('find')->with(8)->willReturn([
            'id'      => 8,
            'user_id' => 4,
        ]);

        $service = new LinkEditor($links, $collections);
        $result  = $service->create([
            'title'             => '  New link  ',
            'url'               => ' https://example.com/new ',
            'collection_id'     => '8',
            'description'       => '',
            'importance'        => 'high',
            'is_favorite'       => false,
            'is_pending_review' => true,
            'is_hidden'         => true,
        ], 99);

        $this->assertSame(EditStatus::CREATED, $result['status']);
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
                'is_hidden'         => 0,
                'user_id'           => 9,
            ])
            ->willReturn(28);

        $collections = $this->createMock(CollectionModel::class);
        $collections->expects($this->never())->method('find');

        $service = new LinkEditor($links, $collections);
        $result  = $service->create([
            'title'         => 'Inbox link',
            'url'           => 'https://example.com/inbox',
            'collection_id' => '',
        ], 9);

        $this->assertSame(EditStatus::CREATED, $result['status']);
        $this->assertSame([], $result['errors']);
    }

    public function testUpdateReturnsNotFoundForMissingLink(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(404)->willReturn(null);
        $links->expects($this->never())->method('update');

        $service = new LinkEditor($links, $this->createMock(CollectionModel::class));
        $result  = $service->update(404, []);

        $this->assertSame(EditStatus::NOT_FOUND, $result['status']);
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

        $service = new LinkEditor($links, $collections);
        $result  = $service->update(3, [
            'title'         => '',
            'url'           => 'not-a-url',
            'collection_id' => '0',
            'importance'    => 'urgent',
        ]);

        $this->assertSame(EditStatus::INVALID, $result['status']);
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

        $service = new LinkEditor($links, $collections);
        $result  = $service->update(3, [
            'title'         => 'Existing title',
            'url'           => 'https://example.com',
            'collection_id' => '',
        ]);

        $this->assertSame(EditStatus::INVALID, $result['status']);
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
                'is_hidden'         => 1,
            ])
            ->willReturn(true);

        $collections = $this->createMock(CollectionModel::class);
        $collections->method('find')->with(8)->willReturn(['id' => 8]);

        $service = new LinkEditor($links, $collections);
        $result  = $service->update(3, [
            'title'             => '  Updated title  ',
            'url'               => ' https://example.com/path ',
            'collection_id'     => '8',
            'description'       => '  Useful note  ',
            'importance'        => '',
            'is_favorite'       => true,
            'is_pending_review' => false,
            'is_hidden'         => true,
        ]);

        $this->assertSame(EditStatus::UPDATED, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteReturnsNotFoundForMissingLink(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->method('find')->with(404)->willReturn(null);
        $links->expects($this->never())->method('delete');

        $service = new LinkEditor($links, $this->createMock(CollectionModel::class));
        $result  = $service->delete(404);

        $this->assertSame(EditStatus::NOT_FOUND, $result['status']);
        $this->assertNull($result['link']);
        $this->assertSame([], $result['errors']);
    }

    public function testDeleteUsesTheSoftDeletingLinkModel(): void
    {
        $links = $this->createMock(LinkModel::class);
        $links->expects($this->once())->method('find')->with(3)->willReturn($this->storedLink());
        $links->expects($this->once())->method('delete')->with(3)->willReturn(true);

        $service = new LinkEditor($links, $this->createMock(CollectionModel::class));
        $result  = $service->delete(3);

        $this->assertSame(EditStatus::DELETED, $result['status']);
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
            'is_hidden'         => '0',
        ];
    }

    private function storedCollection(): array
    {
        return [
            'id'          => '8',
            'user_id'     => '4',
            'name'        => 'Work',
            'description' => null,
            'icon'        => null,
            'is_featured' => '0',
            'is_hidden'   => '0',
            'sort_order'  => '20',
        ];
    }
}

