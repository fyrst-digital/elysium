<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1645716876AddSlideSettings;

class Migration1645716876AddSlideSettingsTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1645716876AddSlideSettings();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_settings', 'json', true);
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1645716876AddSlideSettings();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_settings');
    }
}
