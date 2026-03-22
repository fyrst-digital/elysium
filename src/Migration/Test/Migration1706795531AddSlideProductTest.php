<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1706795531AddSlideProductTest extends TestCase
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

    public function testProductIdColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'product_id']
        );

        static::assertNotFalse($column, 'product_id column should exist in blur_elysium_slides');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'product_id should be binary type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'product_id should be nullable');
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
}
