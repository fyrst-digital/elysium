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

        /**
         * Early return if already migrated
         */
        if ($this->isAlreadyMigrated($connection, $newMediaFolderId)) {
            return;
        }

        $connection->transactional(function (Connection $connection) use ($newMediaFolderId) {
            /**
             * Check if the new default media folder id already exists
             * If it does, we can skip the migration
             */
            $originalFolder = $this->getOriginalMediaFolder($connection);
            if (!$originalFolder) {
                return;
            }

            /**
             * Find all media ids by the original media folder id
             */
            $mediaIds = $this->getMediaIds($connection, $originalFolder['id']);

            /** 
             * first, delete the original media folder by the original media folder id
             * then change the id in the original media folder data to the new default media folder id
             * and insert the new data into the media_folder table
             */
            $this->transformMediaFolder($connection, $originalFolder, $newMediaFolderId);

            /**
             * Update all media ids to the new default media folder id
             */
            if (count($mediaIds) > 0) {
                $this->updateMediaIds($connection, $mediaIds, $newMediaFolderId);
            }
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

    private function getMediaIds(Connection $connection, string $mediaFolderId): array
    {
        return $connection->fetchFirstColumn(
            'SELECT id FROM media WHERE media_folder_id = :folderId',
            ['folderId' => $mediaFolderId]
        );
    }

    function transformMediaFolder(Connection $connection, array $originalFolder, string $newMediaFolderId): void
    {
        $connection->delete('media_folder', [
            'id' => $originalFolder['id'],
        ]);
        $originalFolder['id'] = hex2bin(Defaults::MEDIA_FOLDER_ID);
        $connection->insert('media_folder', $originalFolder);
    }

    function updateMediaIds(Connection $connection, array $mediaIds, string $newMediaFolderId): void
    {
        $connection->executeStatement(
            'UPDATE media SET media_folder_id = :mediaFolderId WHERE id IN (:mediaIds)',
            [
                'mediaFolderId' => $newMediaFolderId,
                'mediaIds' => $mediaIds,
            ],
            [
                'mediaIds' => ArrayParameterType::STRING,
            ]
        );
    }
}
