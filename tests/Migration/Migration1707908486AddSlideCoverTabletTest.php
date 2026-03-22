<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1707908486AddSlideCoverTablet;

class Migration1707908486AddSlideCoverTabletTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1707908486AddSlideCoverTablet();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_tablet_id', 'binary', true);
    }

    public function testMigrationAddsForeignKey(): void
    {
        $migration = new Migration1707908486AddSlideCoverTablet();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_tablet_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1707908486AddSlideCoverTablet();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_tablet_id');
    }
}
