<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1624108754ElysiumSlidesTranslation;

class Migration1624108754ElysiumSlidesTranslationTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationCreatesTable(): void
    {
        $migration = new Migration1624108754ElysiumSlidesTranslation();
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides_translation');
    }

    public function testTableHasRequiredColumns(): void
    {
        $migration = new Migration1624108754ElysiumSlidesTranslation();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides_translation', 'blur_elysium_slides_id', 'binary', false);
        $this->assertColumnExists('blur_elysium_slides_translation', 'language_id', 'binary', false);
        $this->assertColumnExists('blur_elysium_slides_translation', 'name', 'varchar', true);
        $this->assertColumnExists('blur_elysium_slides_translation', 'title', 'varchar', true);
        $this->assertColumnExists('blur_elysium_slides_translation', 'description', 'longtext', true);
        $this->assertColumnExists('blur_elysium_slides_translation', 'button_label', 'varchar', true);
        $this->assertColumnExists('blur_elysium_slides_translation', 'url', 'longtext', true);
        $this->assertColumnExists('blur_elysium_slides_translation', 'created_at', 'datetime', false);
        $this->assertColumnExists('blur_elysium_slides_translation', 'updated_at', 'datetime', true);
    }

    public function testPrimaryKeyExists(): void
    {
        $migration = new Migration1624108754ElysiumSlidesTranslation();
        $this->runMigration($migration);

        $this->assertIndexExists('blur_elysium_slides_translation', 'PRIMARY');
    }

    public function testForeignKeysExist(): void
    {
        $migration = new Migration1624108754ElysiumSlidesTranslation();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides_translation', 'fk.blur_elysium_slides_translation.blur_elysium_slides_id');
        $this->assertForeignKeyExists('blur_elysium_slides_translation', 'fk.blur_elysium_slides_translation.language_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1624108754ElysiumSlidesTranslation();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides_translation');
    }
}
