<?php

use App\Models\LinkModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LinkModelTest extends CIUnitTestCase
{
    public function testDeletesArePermanent(): void
    {
        $model = new class extends LinkModel {
            public function usesSoftDeletes(): bool
            {
                return $this->useSoftDeletes;
            }
        };

        $this->assertFalse($model->usesSoftDeletes());
    }

    public function testImportanceAcceptsSupportedValuesAndNull(): void
    {
        $model = new LinkModel();

        $this->assertTrue($model->validate(['importance' => null]));
        $this->assertTrue($model->validate(['importance' => 'low']));
        $this->assertTrue($model->validate(['importance' => 'medium']));
        $this->assertTrue($model->validate(['importance' => 'high']));
    }

    public function testImportanceRejectsUnsupportedValue(): void
    {
        $model = new LinkModel();

        $this->assertFalse($model->validate(['importance' => 'urgent']));
        $this->assertFalse($model->validate(['importance' => 'normal']));
    }

    public function testHiddenFlagCanBePersisted(): void
    {
        $model = new class extends LinkModel {
            public function allowedFields(): array
            {
                return $this->allowedFields;
            }
        };

        $this->assertContains('is_hidden', $model->allowedFields());
    }
}
