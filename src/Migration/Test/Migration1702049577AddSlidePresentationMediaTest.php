<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1702049577AddSlidePresentationMediaTest extends TestCase
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

    public function testPresentationMediaColumnExists(): void
    {
        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'presentation_media_id']
        );

        static::assertNotFalse($column, 'presentation_media_id column should exist in blur_elysium_slides');
        static::assertStringContainsString('binary', $column['COLUMN_TYPE'], 'presentation_media_id should be binary type');
        static::assertEquals('YES', $column['IS_NULLABLE'], 'presentation_media_id should be nullable');
    }

    public function testForeignKeyExists(): void
    {
        $fk = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'FOREIGN KEY', 'fk.blur_elysium_slides.presentation_media_id']
        );

        static::assertNotFalse($fk, 'Foreign key for presentation_media_id should exist');
    }
}
