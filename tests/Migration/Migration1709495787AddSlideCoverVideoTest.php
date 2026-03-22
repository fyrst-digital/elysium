<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1709495787AddSlideCoverVideo;

class Migration1709495787AddSlideCoverVideoTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1709495787AddSlideCoverVideo();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_video_id', 'binary', true);
    }

    public function testMigrationAddsForeignKey(): void
    {
        $migration = new Migration1709495787AddSlideCoverVideo();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_video_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1709495787AddSlideCoverVideo();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_video_id');
    }
}
