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

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
            'slide_cover_mobile_id' => Uuid::randomBytes(),
            'slide_cover_tablet_id' => Uuid::randomBytes(),
            'slide_cover_video_id' => Uuid::randomBytes(),
            'presentation_media_id' => Uuid::randomBytes(),
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
        static::assertTrue(Uuid::isValid($decoded['slideCover']['mobileId']));
        static::assertTrue(Uuid::isValid($decoded['slideCover']['desktopId']));
        static::assertTrue(Uuid::isValid($decoded['slideCover']['tabletId']));
        static::assertTrue(Uuid::isValid($decoded['slideCover']['videoId']));
        static::assertTrue(Uuid::isValid($decoded['focusImageId']));
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
