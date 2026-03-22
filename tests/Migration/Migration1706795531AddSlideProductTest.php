<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1706795531AddSlideProduct;

class Migration1706795531AddSlideProductTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTable();
    }

    public function testMigrationAddsProductIdColumn(): void
    {
        $migration = new Migration1706795531AddSlideProduct();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'product_id', 'binary', true);
    }

    public function testMigrationAddsProductVersionIdColumn(): void
    {
        $migration = new Migration1706795531AddSlideProduct();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'product_version_id', 'binary', true);
    }

    public function testMigrationAddsForeignKey(): void
    {
        $migration = new Migration1706795531AddSlideProduct();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1706795531AddSlideProduct();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'product_id');
        $this->assertColumnExists('blur_elysium_slides', 'product_version_id');
    }
}
