<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1781000000ConsolidateContentSettings;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1781000000ConsolidateContentSettingsTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testMigrationRunsOnFreshInstall(): void
    {
        $this->createSlideTable();
        $this->createFreshInstallTranslationTable();

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides');
        $this->assertTableExists('blur_elysium_slides_translation');
        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');
    }

    public function testMigrationMigratesTranslationData(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->connection->insert('blur_elysium_slides_translation', [
            'blur_elysium_slides_id' => $slideId,
            'language_id' => $languageId,
            'name' => 'Test Slide',
            'title' => 'Test Title',
            'description' => '<p>Test Description</p>',
            'button_label' => 'Click Me',
            'url' => '/test-url',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'description');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'button_label');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'url');

        $contentSettings = $this->connection->fetchOne(
            'SELECT content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );

        static::assertNotFalse($contentSettings);
        $decoded = json_decode((string) $contentSettings, true);
        static::assertIsArray($decoded);
        static::assertSame('Test Title', $decoded['title']);
        static::assertSame('<p>Test Description</p>', $decoded['description']);
        static::assertSame('Click Me', $decoded['button']['label']);
        static::assertSame('/test-url', $decoded['url']);
    }

    public function testMigrationMigratesMediaIds(): void
    {
        $this->createSlideTable([
            '`slide_cover_id` BINARY(16) NULL',
            '`slide_cover_mobile_id` BINARY(16) NULL',
            '`slide_cover_tablet_id` BINARY(16) NULL',
            '`slide_cover_video_id` BINARY(16) NULL',
            '`presentation_media_id` BINARY(16) NULL',
        ]);
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $mobileCoverId = Uuid::randomBytes();
        $tabletCoverId = Uuid::randomBytes();
        $videoCoverId = Uuid::randomBytes();
        $focusImageId = Uuid::randomBytes();

        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
            'slide_cover_mobile_id' => $mobileCoverId,
            'slide_cover_tablet_id' => $tabletCoverId,
            'slide_cover_video_id' => $videoCoverId,
            'presentation_media_id' => $focusImageId,
        ]);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->connection->insert('blur_elysium_slides_translation', [
            'blur_elysium_slides_id' => $slideId,
            'language_id' => $languageId,
            'name' => 'Test Slide',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_mobile_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_tablet_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_video_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'presentation_media_id');

        $contentSettings = $this->connection->fetchOne(
            'SELECT content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );

        static::assertNotFalse($contentSettings);
        $decoded = json_decode((string) $contentSettings, true);
        static::assertIsArray($decoded);
        static::assertArrayHasKey('slideCover', $decoded);
        static::assertArrayHasKey('focusImageId', $decoded);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        static::assertSame(Uuid::fromBytesToHex($mobileCoverId), $decoded['slideCover']['mobileId']);
        static::assertSame(Uuid::fromBytesToHex($tabletCoverId), $decoded['slideCover']['tabletId']);
        static::assertSame(Uuid::fromBytesToHex($videoCoverId), $decoded['slideCover']['videoId']);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationPreservesProductAndCategoryForeignKeys(): void
    {
        $this->createSlideTableWithProductCategoryAndMediaForeignKeys();
        $this->createFreshInstallTranslationTable();

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.category_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_mobile_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->createSlideTable([
            '`slide_cover_id` BINARY(16) NULL',
            '`slide_cover_mobile_id` BINARY(16) NULL',
            '`slide_cover_tablet_id` BINARY(16) NULL',
            '`slide_cover_video_id` BINARY(16) NULL',
            '`presentation_media_id` BINARY(16) NULL',
        ]);
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->connection->insert('blur_elysium_slides_translation', [
            'blur_elysium_slides_id' => $slideId,
            'language_id' => $languageId,
            'name' => 'Test Slide',
            'title' => 'Test Title',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
    }

    protected function assertColumnDoesNotExist(string $table, string $column, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            [$table, $column]
        );

        static::assertFalse($exists, $message ?: "Column '{$column}' should NOT exist in table '{$table}'");
    }

    protected function assertForeignKeyDoesNotExist(string $table, string $fkName, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\' AND TABLE_SCHEMA = DATABASE()',
            [$table, $fkName]
        );

        static::assertFalse($exists, $message ?: "Foreign key '{$fkName}' should NOT exist in table '{$table}'");
    }

    private function createSlideTableWithProductCategoryAndMediaForeignKeys(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NULL,
                `product_version_id` BINARY(16) NULL,
                `category_id` BINARY(16) NULL,
                `category_version_id` BINARY(16) NULL,
                `slide_cover_id` BINARY(16) NULL,
                `slide_cover_mobile_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.blur_elysium_slides.product_id` FOREIGN KEY (`product_id`, `product_version_id`)
                    REFERENCES `product` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.category_id` FOREIGN KEY (`category_id`, `category_version_id`)
                    REFERENCES `category` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_id` FOREIGN KEY (`slide_cover_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_mobile_id` FOREIGN KEY (`slide_cover_mobile_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createFreshInstallTranslationTable(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `content_settings` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createUpgradeTranslationTable(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `title` VARCHAR(255) NULL,
                `description` TEXT NULL,
                `button_label` VARCHAR(255) NULL,
                `url` VARCHAR(255) NULL,
                `content_settings` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
