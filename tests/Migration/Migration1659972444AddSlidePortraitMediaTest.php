<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1659972444AddSlidePortraitMedia;

class Migration1659972444AddSlidePortraitMediaTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1659972444AddSlidePortraitMedia();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'media_portrait_id', 'binary', true);
    }

    public function testMigrationAddsForeignKey(): void
    {
        $migration = new Migration1659972444AddSlidePortraitMedia();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.media_portrait_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1659972444AddSlidePortraitMedia();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'media_portrait_id');
    }
}
