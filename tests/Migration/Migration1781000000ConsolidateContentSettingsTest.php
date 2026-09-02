<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1781000000ConsolidateContentSettings;
use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1781000000ConsolidateContentSettingsTest extends AbstractMigrationTestCase
{
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

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Test Slide',
            'title' => 'Test Title',
            'description' => '<p>Test Description</p>',
            'button_label' => 'Click Me',
            'url' => '/test-url',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'description');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'button_label');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'url');

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Test Title', $decoded['title']);
        static::assertSame('<p>Test Description</p>', $decoded['description']);
        static::assertSame('Click Me', $decoded['button']['label']);
        static::assertSame('/test-url', $decoded['url']);
    }

    public function testMigrationMigratesMediaIds(): void
    {
        $this->createSlideTableWithAllMediaColumns();
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
        $this->insertTranslation($slideId, $languageId, ['name' => 'Test Slide']);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertAllMediaColumnsDropped();

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        static::assertSame(Uuid::fromBytesToHex($mobileCoverId), $decoded['slideCover']['mobileId']);
        static::assertSame(Uuid::fromBytesToHex($tabletCoverId), $decoded['slideCover']['tabletId']);
        static::assertSame(Uuid::fromBytesToHex($videoCoverId), $decoded['slideCover']['videoId']);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationMigratesCombinedTextAndMedia(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $focusImageId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
            'presentation_media_id' => $focusImageId,
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Combined Slide',
            'title' => 'Headline',
            'description' => '<p>Body</p>',
            'button_label' => 'Go',
            'url' => '/go',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertAllMediaColumnsDropped();

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Headline', $decoded['title']);
        static::assertSame('<p>Body</p>', $decoded['description']);
        static::assertSame('Go', $decoded['button']['label']);
        static::assertSame('/go', $decoded['url']);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationCopiesMediaToEveryTranslationAndKeepsLanguageSpecificCopy(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $otherLanguageId = Uuid::randomBytes();

        $this->insertTranslation($slideId, $systemLanguageId, [
            'name' => 'EN Slide',
            'title' => 'English title',
        ]);
        $this->insertTranslation($slideId, $otherLanguageId, [
            'name' => 'DE Slide',
            'title' => 'German title',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $coverHex = Uuid::fromBytesToHex($desktopCoverId);

        $en = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame('English title', $en['title']);
        static::assertSame($coverHex, $en['slideCover']['desktopId']);

        $de = $this->fetchContentSettings($slideId, $otherLanguageId);
        static::assertSame('German title', $de['title']);
        static::assertSame($coverHex, $de['slideCover']['desktopId']);
    }

    public function testMigrationCreatesSystemLanguageTranslationWhenOnlyOtherLanguageExists(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $otherLanguageId = Uuid::randomBytes();
        $this->insertTranslation($slideId, $otherLanguageId, [
            'name' => 'German only',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $coverHex = Uuid::fromBytesToHex($desktopCoverId);

        $systemName = $this->connection->fetchOne(
            'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $systemLanguageId]
        );
        static::assertSame('German only', $systemName);

        $systemSettings = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame($coverHex, $systemSettings['slideCover']['desktopId']);

        $otherSettings = $this->fetchContentSettings($slideId, $otherLanguageId);
        static::assertSame($coverHex, $otherSettings['slideCover']['desktopId']);
        $this->assertAllMediaColumnsDropped();
    }

    public function testMigrationCreatesSystemLanguageTranslationWhenSlideHasNoTranslations(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $name = $this->connection->fetchOne(
            'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $systemLanguageId]
        );
        static::assertSame(Uuid::fromBytesToHex($slideId), $name);

        $decoded = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        $this->assertAllMediaColumnsDropped();
    }

    public function testMigrationMigratesPresentationMediaWithoutSlideCoverColumn(): void
    {
        $this->createSlideTable([
            '`presentation_media_id` BINARY(16) NULL',
        ]);
        $this->createFreshInstallTranslationTable();

        $focusImageId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'presentation_media_id' => $focusImageId,
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Focus only']);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides', 'presentation_media_id');

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationKeepsExistingContentSettingsWhenLegacyTitleIsEmpty(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Prefilled',
            'title' => null,
            'content_settings' => json_encode(['title' => 'Already in JSON'], \JSON_THROW_ON_ERROR),
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Already in JSON', $decoded['title']);
    }

    public function testMigrationMigratesZeroStringTitle(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Zero title',
            'title' => '0',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('0', $decoded['title']);
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
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Test Slide',
            'title' => 'Test Title',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
    }

    public function testVerifyFailureDoesNotDropMediaColumns(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Unmigrated']);

        $migration = new class extends Migration1781000000ConsolidateContentSettings {
            protected function migrateMediaIds(Connection $connection): void
            {
            }
        };

        try {
            $this->runMigration($migration);
            static::fail('Expected contentSettings verification to fail');
        } catch (\RuntimeException $e) {
            static::assertStringContainsString('verification failed', $e->getMessage());
        }

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_id');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function insertTranslation(string $slideId, string $languageId, array $data): void
    {
        $data['blur_elysium_slides_id'] = $slideId;
        $data['language_id'] = $languageId;
        $data['created_at'] = $data['created_at'] ?? (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->insert('blur_elysium_slides_translation', $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchContentSettings(string $slideId, string $languageId): array
    {
        $contentSettings = $this->connection->fetchOne(
            'SELECT content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );

        static::assertNotFalse($contentSettings);
        $decoded = json_decode((string) $contentSettings, true);
        static::assertIsArray($decoded);

        return $decoded;
    }

    private function createSlideTableWithAllMediaColumns(): void
    {
        $this->createSlideTable([
            '`slide_cover_id` BINARY(16) NULL',
            '`slide_cover_mobile_id` BINARY(16) NULL',
            '`slide_cover_tablet_id` BINARY(16) NULL',
            '`slide_cover_video_id` BINARY(16) NULL',
            '`presentation_media_id` BINARY(16) NULL',
        ]);
    }

    private function assertAllMediaColumnsDropped(): void
    {
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_mobile_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_tablet_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_video_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'presentation_media_id');
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
