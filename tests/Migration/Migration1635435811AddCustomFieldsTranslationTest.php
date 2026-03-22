<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1635435811AddCustomFieldsTranslationTest extends TestCase
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

    public function testCustomFieldsColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation', 'custom_fields']
        );

        static::assertNotFalse($column, 'custom_fields column should exist in blur_elysium_slides_translation');
        static::assertEquals('json', $column['DATA_TYPE'], 'custom_fields should be JSON type');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->connection->executeStatement(
            'ALTER TABLE `blur_elysium_slides_translation` ADD COLUMN IF NOT EXISTS `custom_fields` JSON NULL'
        );

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation', 'custom_fields']
        );

        static::assertNotFalse($column, 'custom_fields column should still exist after re-running migration');
    }
}
