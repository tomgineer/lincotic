<?php

use App\Models\CollectionModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CollectionModelTest extends CIUnitTestCase
{
    public function testHiddenFlagCanBePersisted(): void
    {
        $model = new class extends CollectionModel {
            public function allowedFields(): array
            {
                return $this->allowedFields;
            }
        };

        $this->assertContains('is_hidden', $model->allowedFields());
    }
}
