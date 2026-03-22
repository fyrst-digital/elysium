<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1624108754ElysiumSlidesTranslationTest extends TestCase
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

    public function testTranslationTableExists(): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation']
        );

        static::assertNotFalse($exists, 'Table blur_elysium_slides_translation should exist');
    }

    public function testTableHasRequiredColumns(): void
    {
        $columns = $this->connection->fetchAll(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation']
        );

        $columnNames = array_column($columns, 'COLUMN_NAME');

        static::assertContains('blur_elysium_slides_id', $columnNames, 'Table should have blur_elysium_slides_id column');
        static::assertContains('language_id', $columnNames, 'Table should have language_id column');
        static::assertContains('name', $columnNames, 'Table should have name column');
        static::assertContains('title', $columnNames, 'Table should have title column');
        static::assertContains('description', $columnNames, 'Table should have description column');
        static::assertContains('button_label', $columnNames, 'Table should have button_label column');
        static::assertContains('url', $columnNames, 'Table should have url column');
        static::assertContains('created_at', $columnNames, 'Table should have created_at column');
        static::assertContains('updated_at', $columnNames, 'Table should have updated_at column');
    }

    public function testPrimaryKeyExists(): void
    {
        $pk = $this->connection->fetchAll(
            'SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ?',
            ['blur_elysium_slides_translation', 'PRIMARY']
        );

        $pkColumns = array_column($pk, 'COLUMN_NAME');

        static::assertContains('blur_elysium_slides_id', $pkColumns, 'Primary key should include blur_elysium_slides_id');
        static::assertContains('language_id', $pkColumns, 'Primary key should include language_id');
    }

    public function testForeignKeysExist(): void
    {
        $fks = $this->connection->fetchAll(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides_translation', 'FOREIGN KEY']
        );

        $fkNames = array_column($fks, 'CONSTRAINT_NAME');

        static::assertContains('fk.blur_elysium_slides_translation.blur_elysium_slides_id', $fkNames, 'Foreign key to slides should exist');
        static::assertContains('fk.blur_elysium_slides_translation.language_id', $fkNames, 'Foreign key to language should exist');
    }
}
