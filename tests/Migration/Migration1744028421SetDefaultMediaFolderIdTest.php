<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Migration\Migration1744028421SetDefaultMediaFolderId;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1744028421SetDefaultMediaFolderIdTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupExistingMediaFolder();
    }

    protected function tearDown(): void
    {
        // Clean up in reverse dependency order (child tables first)

        // Delete media entries created by this test
        $this->connection->executeStatement(
            "DELETE FROM media WHERE file_name LIKE 'test-image%'"
        );

        // Clean up media folders created by tests
        $defaultFolderId = $this->connection->fetchOne(
            'SELECT id FROM media_default_folder WHERE entity = ?',
            ['blur_elysium_slides']
        );

        if ($defaultFolderId) {
            // Delete any media folders linked to our default folder
            $this->connection->executeStatement(
                'DELETE FROM media_folder WHERE default_folder_id = ?',
                [$defaultFolderId]
            );
        }

        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');
        parent::tearDown();
    }

    public function testMigrationCreatesMediaFolderWithCorrectId(): void
    {
        $this->createMediaDefaultFolder();
        $this->createOriginalMediaFolder();

        $migration = new Migration1744028421SetDefaultMediaFolderId();
        $this->runMigration($migration);

        $newFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);
        $folder = $this->connection->fetchAssociative(
            'SELECT id FROM media_folder WHERE id = ?',
            [$newFolderId]
        );

        static::assertNotFalse($folder, 'Media folder with default ID should exist');
    }

    public function testMigrationMigratesMediaToNewFolder(): void
    {
        $this->createMediaDefaultFolder();
        $oldFolderId = $this->createOriginalMediaFolder();

        // Create a media entry in the old folder
        $mediaId = Uuid::randomBytes();
        $this->connection->insert('media', [
            'id' => $mediaId,
            'media_folder_id' => $oldFolderId,
            'file_name' => 'test-image',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'created_at' => (new \DateTime())->format(\Shopware\Core\Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $migration = new Migration1744028421SetDefaultMediaFolderId();
        $this->runMigration($migration);

        // Verify media was moved to new folder
        $newFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);
        $media = $this->connection->fetchAssociative(
            'SELECT media_folder_id FROM media WHERE id = ?',
            [$mediaId]
        );

        static::assertEquals($newFolderId, $media['media_folder_id'], 'Media should be moved to new folder');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->createMediaDefaultFolder();
        $this->createOriginalMediaFolder();

        $migration = new Migration1744028421SetDefaultMediaFolderId();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $newFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);
        $folder = $this->connection->fetchAssociative(
            'SELECT id FROM media_folder WHERE id = ?',
            [$newFolderId]
        );

        static::assertNotFalse($folder, 'Media folder should still exist after re-running migration');
    }

    private function cleanupExistingMediaFolder(): void
    {
        // Get the default folder ID for blur_elysium_slides
        $defaultFolderId = $this->connection->fetchOne(
            'SELECT id FROM media_default_folder WHERE entity = ?',
            ['blur_elysium_slides']
        );

        if ($defaultFolderId) {
            // Delete any existing media folder linked to this default folder
            $this->connection->executeStatement(
                'DELETE FROM media_folder WHERE default_folder_id = ?',
                [$defaultFolderId]
            );
        }
    }

    private function createMediaDefaultFolder(): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM media_default_folder WHERE entity = ?',
            ['blur_elysium_slides']
        );

        if (!$exists) {
            $this->connection->insert('media_default_folder', [
                'id' => Uuid::randomBytes(),
                'entity' => 'blur_elysium_slides',
                'created_at' => (new \DateTime())->format(\Shopware\Core\Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }
    }

    private function createOriginalMediaFolder(): string
    {
        $defaultFolderId = $this->connection->fetchOne(
            'SELECT id FROM media_default_folder WHERE entity = ?',
            ['blur_elysium_slides']
        );

        $folderId = Uuid::randomBytes();
        $this->connection->insert('media_folder', [
            'id' => $folderId,
            'name' => Defaults::MEDIA_FOLDER_NAME . ' (old)',
            'default_folder_id' => $defaultFolderId,
            'created_at' => (new \DateTime())->format(\Shopware\Core\Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        return $folderId;
    }
}
