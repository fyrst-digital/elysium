<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1750171200AddSlideTimeControl;

class Migration1750171200AddSlideTimeControlTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumns(): void
    {
        $migration = new Migration1750171200AddSlideTimeControl();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'active_from', 'datetime', true);
        $this->assertColumnExists('blur_elysium_slides', 'active_until', 'datetime', true);
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1750171200AddSlideTimeControl();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'active_from');
        $this->assertColumnExists('blur_elysium_slides', 'active_until');
    }
}
