<?php

use App\Models\LinkModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LinkModelTest extends CIUnitTestCase
{
    public function testImportanceAcceptsSupportedValuesAndNull(): void
    {
        $model = new LinkModel();

        $this->assertTrue($model->validate(['importance' => null]));
        $this->assertTrue($model->validate(['importance' => 'low']));
        $this->assertTrue($model->validate(['importance' => 'normal']));
        $this->assertTrue($model->validate(['importance' => 'high']));
    }

    public function testImportanceRejectsUnsupportedValue(): void
    {
        $model = new LinkModel();

        $this->assertFalse($model->validate(['importance' => 'urgent']));
    }

    public function testBlankImportanceIsNormalizedToNull(): void
    {
        $model = new class extends LinkModel {
            public function normalize(array $data): array
            {
                return $this->normalizeImportance($data);
            }
        };

        $data = $model->normalize(['data' => ['importance' => '']]);

        $this->assertNull($data['data']['importance']);
    }
}
