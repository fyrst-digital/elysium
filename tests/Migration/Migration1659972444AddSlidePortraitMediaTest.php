<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1659972444AddSlidePortraitMediaTest extends TestCase
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

    public function testSlideCoverMobileColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'slide_cover_mobile_id']
        );

        static::assertNotFalse($column, 'slide_cover_mobile_id column should exist in blur_elysium_slides');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'slide_cover_mobile_id should be binary type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'slide_cover_mobile_id should be nullable');
    }

    public function testForeignKeyExists(): void
    {
        $fk = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'FOREIGN KEY', 'fk.blur_elysium_slides.slide_cover_mobile_id']
        );

        static::assertNotFalse($fk, 'Foreign key for slide_cover_mobile_id should exist');
    }
}
