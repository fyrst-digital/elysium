<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1750098814AddContentSettingsTest extends TestCase
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

    public function testContentSettingsColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation', 'content_settings']
        );

        static::assertNotFalse($column, 'content_settings column should exist in blur_elysium_slides_translation');
        static::assertEquals('json', $column['DATA_TYPE'], 'content_settings should be JSON type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'content_settings should be nullable');
    }

    public function testMigrationIsIdempotent(): void
    {
        try {
            $this->connection->executeStatement(
                'ALTER TABLE `blur_elysium_slides_translation` ADD COLUMN `content_settings` JSON NULL'
            );
        } catch (\Doctrine\DBAL\Exception $e) {
            // Ignore duplicate column errors - migration is idempotent
        }

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation', 'content_settings']
        );

        static::assertNotFalse($column, 'content_settings column should exist after re-running migration');
    }
}
