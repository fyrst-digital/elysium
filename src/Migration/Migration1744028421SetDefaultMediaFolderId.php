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

        if ($this->isAlreadyMigrated($connection, $newMediaFolderId)) {
            return;
        }

        $connection->transactional(function (Connection $connection) use ($newMediaFolderId) {
            $originalFolder = $this->getOriginalMediaFolder($connection);
            if (!$originalFolder) {
                return;
            }

            $mediaIds = $this->getMediaIds($connection, $originalFolder['id']);

            $this->transformMediaFolder($connection, $originalFolder, $newMediaFolderId);

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
