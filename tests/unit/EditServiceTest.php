<?php

use App\Models\CollectionModel;
use App\Models\LinkModel;
use App\Services\EditService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EditServiceTest extends CIUnitTestCase
{
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
}
