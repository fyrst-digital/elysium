<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1645716876AddSlideSettingsTest extends TestCase
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

    public function testSlideSettingsColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'slide_settings']
        );

        static::assertNotFalse($column, 'slide_settings column should exist in blur_elysium_slides');
        static::assertEquals('json', $column['DATA_TYPE'], 'slide_settings should be JSON type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'slide_settings should be nullable');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->connection->executeStatement(
            'ALTER TABLE `blur_elysium_slides` ADD COLUMN IF NOT EXISTS `slide_settings` JSON NULL'
        );

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'slide_settings']
        );

        static::assertNotFalse($column, 'slide_settings column should still exist after re-running migration');
    }
}
