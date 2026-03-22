<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1702049577AddSlidePresentationMedia;

class Migration1702049577AddSlidePresentationMediaTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1702049577AddSlidePresentationMedia();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'presentation_media_id', 'binary', true);
    }

    public function testMigrationAddsForeignKey(): void
    {
        $migration = new Migration1702049577AddSlidePresentationMedia();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.presentation_media_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1702049577AddSlidePresentationMedia();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'presentation_media_id');
    }
}
