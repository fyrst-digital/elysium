<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1707906587ChangeMediaToSlideCoverTest extends TestCase
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

    public function testSlideCoverIdColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'slide_cover_id']
        );

        static::assertNotFalse($column, 'slide_cover_id column should exist in blur_elysium_slides');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'slide_cover_id should be binary type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'slide_cover_id should be nullable');
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
    }

    public function testOldMediaIdColumnDoesNotExist(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'media_id']
        );

        static::assertFalse($column, 'Old media_id column should not exist (renamed to slide_cover_id)');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->connection->executeStatement(
            'ALTER TABLE `blur_elysium_slides` RENAME COLUMN IF EXISTS `media_id` TO `slide_cover_id`'
        );

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'slide_cover_id']
        );

        static::assertNotFalse($column, 'slide_cover_id column should exist after re-running migration');
    }
}
