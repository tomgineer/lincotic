<?php

use App\Models\LinkContentModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LinkContentModelTest extends CIUnitTestCase
{
    public function testContentAndLinkIdCanBePersistedWithApplicationTimestamps(): void
    {
        $model = new class extends LinkContentModel {
            public function configuration(): array
            {
                return [
                    'allowedFields'    => $this->allowedFields,
                    'primaryKey'       => $this->primaryKey,
                    'useAutoIncrement' => $this->useAutoIncrement,
                    'useTimestamps'    => $this->useTimestamps,
                    'updatedField'     => $this->updatedField,
                ];
            }
        };

        $configuration = $model->configuration();

        $this->assertSame(['link_id', 'content'], $configuration['allowedFields']);
        $this->assertSame('link_id', $configuration['primaryKey']);
        $this->assertFalse($configuration['useAutoIncrement']);
        $this->assertTrue($configuration['useTimestamps']);
        $this->assertSame('updated_at', $configuration['updatedField']);
    }
}
