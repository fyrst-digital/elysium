<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744028421SetDefaultMediaFolderId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744028421;
    }

    public function update(Connection $connection): void
    {
        $newMediaFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);

        // Early return if already migrated
        if ($this->isAlreadyMigrated($connection, $newMediaFolderId)) {
            return;
        }

        $connection->transactional(function (Connection $connection) use ($newMediaFolderId) {
            // Get original folder and validate it exists
            $originalFolder = $this->getOriginalMediaFolder($connection);
            if (!$originalFolder) {
                return; // Nothing to migrate
            }

            // Update media records first (referential integrity)
            $this->updateMediaFolderReferences($connection, $originalFolder['id'], $newMediaFolderId);

            // Update folder ID in single operation
            $connection->update(
                'media_folder',
                ['id' => $newMediaFolderId],
                ['id' => $originalFolder['id']]
            );
        });
    }

    private function isAlreadyMigrated(Connection $connection, string $mediaFolderId): bool
    {
        return (bool) $connection->fetchOne(
            'SELECT 1 FROM media_folder WHERE id = :id',
            ['id' => $mediaFolderId]
        );
    }

    private function getOriginalMediaFolder(Connection $connection): ?array
    {
        $result = $connection->fetchAssociative(
            'SELECT mf.* FROM media_folder mf
            JOIN media_default_folder mdf ON mf.default_folder_id = mdf.id
            WHERE mdf.entity = :entity',
            ['entity' => 'blur_elysium_slides']
        );

        return $result ?: null;
    }

    private function updateMediaFolderReferences(Connection $connection, string $oldFolderId, string $newFolderId): void
    {
        $connection->executeStatement(
            'UPDATE media SET media_folder_id = :newId WHERE media_folder_id = :oldId',
            ['newId' => $newFolderId, 'oldId' => $oldFolderId]
        );
    }
}
