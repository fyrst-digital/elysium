<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1750099000AddSlideProductVersionId;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1750099000AddSlideProductVersionIdTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTableWithProductId();
    }

    public function testMigrationAddsProductVersionIdColumn(): void
    {
        $migration = new Migration1750099000AddSlideProductVersionId();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'product_version_id', 'binary', true);
    }

    public function testMigrationAddsForeignKeyConstraint(): void
    {
        $migration = new Migration1750099000AddSlideProductVersionId();
        $this->runMigration($migration);

        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
    }

    public function testMigrationPopulatesProductVersionIdFromProduct(): void
    {
        $productId = $this->insertProduct([
            'product_number' => 'TEST-PRODUCT-001',
        ]);

        $product = $this->connection->fetchAssociative(
            'SELECT version_id FROM product WHERE id = ?',
            [$productId]
        );

        $slideId = $this->insertSlide([
            'product_id' => $productId,
            'product_version_id' => null,
        ]);

        $migration = new Migration1750099000AddSlideProductVersionId();
        $this->runMigration($migration);

        $slide = $this->connection->fetchAssociative(
            'SELECT product_version_id FROM blur_elysium_slides WHERE id = ?',
            [$slideId]
        );

        static::assertEquals(
            $product['version_id'],
            $slide['product_version_id'],
            'product_version_id should be populated from product table'
        );
    }

    public function testMigrationHandlesSlidesWithoutProduct(): void
    {
        $slideId = $this->insertSlide([
            'product_id' => null,
            'product_version_id' => null,
        ]);

        $migration = new Migration1750099000AddSlideProductVersionId();
        $this->runMigration($migration);

        $slide = $this->connection->fetchAssociative(
            'SELECT product_id, product_version_id FROM blur_elysium_slides WHERE id = ?',
            [$slideId]
        );

        static::assertNull($slide['product_id']);
        static::assertNull($slide['product_version_id']);
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1750099000AddSlideProductVersionId();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'product_version_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
    }
}
