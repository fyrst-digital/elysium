<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1750098814AddContentSettings;

class Migration1750098814AddContentSettingsTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
        $this->createSlideTranslationTable();
    }

    public function testMigrationAddsColumn(): void
    {
        $migration = new Migration1750098814AddContentSettings();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings', 'json', true);
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1750098814AddContentSettings();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');
    }
}
