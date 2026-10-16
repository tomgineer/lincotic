<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;
use Config\Services;

/**
 * @internal
 */
final class MultiUserHubTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        Services::resetSingle('auth');
        Services::resetSingle('hubOwnerResolver');
        Services::resetSingle('hubAccessPolicy');
        Services::resetSingle('hubPageCache');

        $this->db = db_connect();
        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        Services::resetSingle('auth');
        Services::resetSingle('hubOwnerResolver');
        Services::resetSingle('hubAccessPolicy');
        Services::resetSingle('hubPageCache');

        parent::tearDown();
    }

    public function testRootAndNamedHubsAreScopedToTheirOwners(): void
    {
        $root = $this->get('/');
        $root->assertStatus(200);
        $root->assertSee('Civic Public');
        $root->assertDontSee('Tom Public');

        Services::resetSingle('auth');
        $tom = $this->get('/@tom');
        $tom->assertStatus(200);
        $tom->assertSee('Tom Public');
        $tom->assertDontSee('Civic Public');
        $tom->assertDontSee('Tom Secret');
    }

    public function testInactiveUsersAndTheirLinksAreNotPublic(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->get('/@blocked');
    }

    public function testInactiveUsersCannotUseDirectLinkRedirects(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->get('/go/6');
    }

    public function testOwnerCanSeeHiddenContentAndEditingControls(): void
    {
        $response = $this
            ->withSession(['user' => ['id' => 2]])
            ->get('/@tom');

        $response->assertStatus(200);
        $response->assertSee('Tom Secret');
        $response->assertSee('Tom Hidden Collection Link');
        $body = (string) $response->getBody();
        $this->assertStringContainsString('data-edit-mode-toggle', $body);
        $this->assertStringNotContainsString('popovertarget="users-popover"', $body);
        $this->assertSame(site_url('@tom'), config('Auth')->loginRedirect());
    }

    public function testAuthenticatedUserCannotLoadAnotherUsersLinkEditor(): void
    {
        $response = $this
            ->withSession(['user' => ['id' => 1]])
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/edit/links/2');

        $response->assertStatus(404);
        $response->assertJSONFragment(['error' => 'Link not found.']);
    }

    public function testStandardUserCanLoadTheirOwnLinkEditor(): void
    {
        $response = $this
            ->withSession(['user' => ['id' => 2]])
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/edit/links/2');

        $response->assertStatus(200);
        $payload = json_decode((string) $response->getJSON(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('Tom Public', $payload['link']['title']);
    }

    public function testInactiveUserWithAnExistingSessionCannotEdit(): void
    {
        $response = $this
            ->withSession(['user' => ['id' => 3]])
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/edit/links/6');

        $response->assertRedirectTo(site_url('login'));
    }

    public function testSearchIncludesUnassignedLinks(): void
    {
        $response = $this->get('/@tom/search?q=Inbox');

        $response->assertStatus(200);
        $response->assertSee('Unassigned Links');
        $response->assertSee('Tom Inbox Match');
    }

    public function testSelectedCollectionMetadataIsRenderedInTheLayout(): void
    {
        $response = $this->get('/@tom?collection=2&sort=title');

        $response->assertStatus(200);
        $body = (string) $response->getBody();

        $this->assertStringContainsString('<title>Tom by Tom | Lincotic</title>', $body);
        $this->assertStringContainsString(
            '<meta name="description" content="A public collection from Tom.">',
            $body,
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="' . site_url('@tom') . '?collection=2">',
            $body,
        );
        $this->assertStringNotContainsString('sort=title', $this->canonicalTag($body));
    }

    private function canonicalTag(string $body): string
    {
        preg_match('/<link rel="canonical"[^>]*>/', $body, $matches);

        return $matches[0] ?? '';
    }

    private function createSchema(): void
    {
        $forge = Database::forge($this->db);

        foreach (['links', 'collections', 'auth_groups_users', 'auth_remember_tokens', 'users', 'settings'] as $table) {
            $forge->dropTable($table, true);
        }

        $forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'class'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 31, 'default' => 'string'],
            'context'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('settings');

        $forge->addField([
            'id'             => ['type' => 'INTEGER', 'auto_increment' => true],
            'username'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_message' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'active'         => ['type' => 'INTEGER', 'default' => 1],
            'last_active'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('users');

        $forge->addField([
            'id'              => ['type' => 'INTEGER', 'auto_increment' => true],
            'selector'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'hashedValidator' => ['type' => 'VARCHAR', 'constraint' => 255],
            'user_id'         => ['type' => 'INTEGER'],
            'expires'         => ['type' => 'DATETIME'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('auth_remember_tokens');

        $forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'user_id'    => ['type' => 'INTEGER'],
            'group'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('auth_groups_users');

        $forge->addField([
            'id'          => ['type' => 'INTEGER', 'auto_increment' => true],
            'user_id'     => ['type' => 'INTEGER'],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'icon'        => ['type' => 'TEXT', 'null' => true],
            'is_featured' => ['type' => 'INTEGER', 'default' => 0],
            'is_hidden'   => ['type' => 'INTEGER', 'default' => 0],
            'sort_order'  => ['type' => 'INTEGER', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('collections');

        $forge->addField([
            'id'                => ['type' => 'INTEGER', 'auto_increment' => true],
            'user_id'           => ['type' => 'INTEGER'],
            'collection_id'     => ['type' => 'INTEGER', 'null' => true],
            'title'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'url'               => ['type' => 'TEXT'],
            'description'       => ['type' => 'TEXT', 'null' => true],
            'importance'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'is_favorite'       => ['type' => 'INTEGER', 'default' => 0],
            'is_pending_review' => ['type' => 'INTEGER', 'default' => 0],
            'is_hidden'         => ['type' => 'INTEGER', 'default' => 0],
            'click_count'       => ['type' => 'INTEGER', 'default' => 0],
            'last_clicked_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('links');
    }

    private function seedData(): void
    {
        $this->db->table('users')->insertBatch([
            ['id' => 1, 'username' => 'civic', 'status' => null, 'active' => 1],
            ['id' => 2, 'username' => 'tom', 'status' => null, 'active' => 1],
            ['id' => 3, 'username' => 'blocked', 'status' => 'banned', 'active' => 1],
        ]);
        $this->db->table('auth_groups_users')->insertBatch([
            ['user_id' => 1, 'group' => 'admin'],
            ['user_id' => 2, 'group' => 'user'],
        ]);
        $this->db->table('collections')->insertBatch([
            ['id' => 1, 'user_id' => 1, 'name' => 'Civic', 'description' => null, 'is_featured' => 1, 'is_hidden' => 0, 'sort_order' => 10],
            ['id' => 2, 'user_id' => 2, 'name' => 'Tom', 'description' => 'A public collection from Tom.', 'is_featured' => 1, 'is_hidden' => 0, 'sort_order' => 10],
            ['id' => 3, 'user_id' => 2, 'name' => 'Tom Private', 'description' => null, 'is_featured' => 1, 'is_hidden' => 1, 'sort_order' => 20],
            ['id' => 4, 'user_id' => 3, 'name' => 'Blocked', 'description' => null, 'is_featured' => 1, 'is_hidden' => 0, 'sort_order' => 10],
        ]);
        $this->db->table('links')->insertBatch([
            ['id' => 1, 'user_id' => 1, 'collection_id' => 1, 'title' => 'Civic Public', 'url' => 'https://civic.example', 'is_hidden' => 0],
            ['id' => 2, 'user_id' => 2, 'collection_id' => 2, 'title' => 'Tom Public', 'url' => 'https://tom.example', 'is_hidden' => 0],
            ['id' => 3, 'user_id' => 2, 'collection_id' => 2, 'title' => 'Tom Secret', 'url' => 'https://secret.example', 'is_hidden' => 1],
            ['id' => 4, 'user_id' => 2, 'collection_id' => 3, 'title' => 'Tom Hidden Collection Link', 'url' => 'https://hidden.example', 'is_hidden' => 0],
            ['id' => 5, 'user_id' => 2, 'collection_id' => null, 'title' => 'Tom Inbox Match', 'url' => 'https://inbox.example', 'is_hidden' => 0],
            ['id' => 6, 'user_id' => 3, 'collection_id' => 4, 'title' => 'Blocked Public', 'url' => 'https://blocked.example', 'is_hidden' => 0],
        ]);
    }
}
