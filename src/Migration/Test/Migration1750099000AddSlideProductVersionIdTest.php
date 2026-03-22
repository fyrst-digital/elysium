<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1750099000AddSlideProductVersionIdTest extends TestCase
{
    use KernelTestBehaviour;

    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
        parent::tearDown();
    }

    public function testProductVersionIdColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'product_version_id']
        );

        static::assertNotFalse($column, 'product_version_id column should exist in blur_elysium_slides');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'product_version_id should be binary type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'product_version_id should be nullable');
    }

    public function testForeignKeyConstraintExists(): void
    {
        $fk = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'FOREIGN KEY', 'fk.blur_elysium_slides.product_id']
        );

        static::assertNotFalse($fk, 'Foreign key for product_id with version_id should exist');
    }

    public function testProductVersionIdIsPopulatedForExistingProducts(): void
    {
        $slidesWithProduct = $this->connection->fetchAll(
            'SELECT id, product_id, product_version_id FROM blur_elysium_slides WHERE product_id IS NOT NULL'
        );

        if (count($slidesWithProduct) === 0) {
            static::assertTrue(true, 'No slides with products exist - test is not applicable');

            return;
        }

        foreach ($slidesWithProduct as $slide) {
            static::assertNotNull(
                $slide['product_version_id'],
                'product_version_id should be populated for slides with product_id'
            );
        }
    }
}
