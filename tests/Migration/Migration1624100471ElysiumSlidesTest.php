<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1624100471ElysiumSlides;

class Migration1624100471ElysiumSlidesTest extends AbstractMigrationTestCase
{
    public function testMigrationCreatesTable(): void
    {
        $migration = new Migration1624100471ElysiumSlides();
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides');
    }

    public function testTableHasRequiredColumns(): void
    {
        $migration = new Migration1624100471ElysiumSlides();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'id', 'binary', false);
        $this->assertColumnExists('blur_elysium_slides', 'translations', 'binary', true);
        $this->assertColumnExists('blur_elysium_slides', 'media_id', 'binary', true);
        $this->assertColumnExists('blur_elysium_slides', 'created_at', 'datetime', false);
        $this->assertColumnExists('blur_elysium_slides', 'updated_at', 'datetime', true);
    }

    public function testPrimaryKeyExists(): void
    {
        $migration = new Migration1624100471ElysiumSlides();
        $this->runMigration($migration);

        $this->assertIndexExists('blur_elysium_slides', 'PRIMARY');
    }

    public function testForeignKeyToMediaExists(): void
    {
        $migration = new Migration1624100471ElysiumSlides();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.media_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1624100471ElysiumSlides();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides');
    }
}
