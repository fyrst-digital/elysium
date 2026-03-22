<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1635435811AddCustomFieldsTranslation;

class Migration1635435811AddCustomFieldsTranslationTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
        $this->createSlideTranslationTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1635435811AddCustomFieldsTranslation();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides_translation', 'custom_fields', 'json', true);
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1635435811AddCustomFieldsTranslation();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides_translation', 'custom_fields');
    }
}
