<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

abstract class AbstractMigrationTestCase extends TestCase
{
    use KernelTestBehaviour;

    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->getContainer()->get(Connection::class);
    }

    protected function tearDown(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');
        parent::tearDown();
    }

    protected function runMigration(MigrationStep $migration): void
    {
        $migration->update($this->connection);
    }

    protected function dropTableIfExists(string $table): void
    {
        $this->connection->executeStatement("DROP TABLE IF EXISTS `{$table}`");
    }

    protected function assertTableExists(string $table, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            [$table]
        );

        static::assertNotFalse($exists, $message ?: "Table '{$table}' should exist");
    }

    protected function assertColumnExists(string $table, string $column, ?string $typeContains = null, ?bool $nullable = null): void
    {
        $row = $this->connection->fetchAssociative(
            'SELECT COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            [$table, $column]
        );

        static::assertNotFalse($row, "Column '{$column}' should exist in table '{$table}'");

        if ($typeContains !== null) {
            $columnType = strtolower($row['COLUMN_TYPE']);
            $expectedType = strtolower($typeContains);
            
            // MariaDB stores JSON as LONGTEXT, so accept both
            if ($expectedType === 'json') {
                static::assertTrue(
                    str_contains($columnType, 'json') || str_contains($columnType, 'longtext'),
                    "Column '{$column}' should be JSON type (MariaDB uses LONGTEXT for JSON)"
                );
            } else {
                static::assertStringContainsString(
                    $expectedType,
                    $columnType,
                    "Column '{$column}' should have type containing '{$typeContains}'"
                );
            }
        }

        if ($nullable !== null) {
            $expected = $nullable ? 'YES' : 'NO';
            static::assertEquals(
                $expected,
                $row['IS_NULLABLE'],
                "Column '{$column}' nullable should be " . ($nullable ? 'YES' : 'NO')
            );
        }
    }

    protected function assertForeignKeyExists(string $table, string $fkName, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\' AND TABLE_SCHEMA = DATABASE()',
            [$table, $fkName]
        );

        static::assertNotFalse($exists, $message ?: "Foreign key '{$fkName}' should exist in table '{$table}'");
    }

    protected function assertIndexExists(string $table, string $indexName, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_NAME = ? AND INDEX_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            [$table, $indexName]
        );

        static::assertNotFalse($exists, $message ?: "Index '{$indexName}' should exist in table '{$table}'");
    }

    protected function createSlideTable(array $additionalColumns = []): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');

        $columnsSql = implode(",\n            ", array_merge([
            '`id` BINARY(16) NOT NULL',
            '`created_at` DATETIME(3) NOT NULL',
            '`updated_at` DATETIME(3) NULL',
        ], $additionalColumns));

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides` (
                {$columnsSql},
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    protected function createSlideTableWithMediaColumns(): void
    {
        $this->createSlideTable([
            '`media_id` BINARY(16) NULL',
            '`media_portrait_id` BINARY(16) NULL',
        ]);
    }

    protected function createSlideTableWithProductId(): void
    {
        $this->createSlideTable([
            '`product_id` BINARY(16) NULL',
            '`product_version_id` BINARY(16) NULL',
        ]);
    }

    protected function createSlideTranslationTable(array $additionalColumns = []): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $columnsSql = implode(",\n            ", array_merge([
            '`blur_elysium_slides_id` BINARY(16) NOT NULL',
            '`language_id` BINARY(16) NOT NULL',
            '`name` VARCHAR(255) NULL',
            '`title` VARCHAR(255) NULL',
            '`description` TEXT NULL',
            '`button_label` VARCHAR(255) NULL',
            '`url` VARCHAR(255) NULL',
            '`created_at` DATETIME(3) NOT NULL',
            '`updated_at` DATETIME(3) NULL',
        ], $additionalColumns));

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                {$columnsSql},
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    protected function insertSlide(array $data): string
    {
        $id = $data['id'] ?? Uuid::randomBytes();
        $data['id'] = $id;
        $data['created_at'] = $data['created_at'] ?? (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->insert('blur_elysium_slides', $data);

        return $id;
    }

    protected function insertProduct(array $data): string
    {
        $id = $data['id'] ?? Uuid::randomBytes();
        $data['id'] = $id;
        $data['version_id'] = $data['version_id'] ?? Uuid::randomBytes();
        $data['product_number'] = $data['product_number'] ?? 'TEST-' . random_int(1000, 9999);
        $data['stock'] = $data['stock'] ?? 1;
        $data['created_at'] = $data['created_at'] ?? (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->insert('product', $data);

        return $id;
    }

    protected function getLanguageId(): string
    {
        return $this->connection->fetchOne('SELECT id FROM language WHERE id = UNHEX(?)', [Defaults::LANGUAGE_SYSTEM]);
    }
}
