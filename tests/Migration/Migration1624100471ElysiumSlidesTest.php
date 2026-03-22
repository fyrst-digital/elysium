<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1624100471ElysiumSlidesTest extends TestCase
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

    public function testTableExists(): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides']
        );

        static::assertNotFalse($exists, 'Table blur_elysium_slides should exist');
    }

    public function testTableHasRequiredColumns(): void
    {
        $columns = $this->connection->fetchAll(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides']
        );

        $columnNames = array_column($columns, 'COLUMN_NAME');

        static::assertContains('id', $columnNames, 'Table should have id column');
        static::assertContains('translations', $columnNames, 'Table should have translations column');
        static::assertContains('media_id', $columnNames, 'Table should have media_id column');
        static::assertContains('created_at', $columnNames, 'Table should have created_at column');
        static::assertContains('updated_at', $columnNames, 'Table should have updated_at column');
    }

    public function testIdColumnIsBinary16(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'id']
        );

        static::assertNotFalse($column, 'id column should exist');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'id column should be binary');
    }

    public function testForeignKeyExists(): void
    {
        $fk = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'FOREIGN KEY', 'fk.blur_elysium_slides.media_id']
        );

        static::assertNotFalse($fk, 'Foreign key for media_id should exist');
    }
}
