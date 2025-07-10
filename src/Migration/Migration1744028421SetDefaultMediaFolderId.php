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
        $countMediaDefaultFolder = $connection->fetchOne(
            '
            SELECT COUNT(*) 
            FROM media_folder mf
            WHERE mf.id = UNHEX(:mediaFolderId);
        ',
            [
                'mediaFolderId' => Defaults::MEDIA_FOLDER_ID,
            ]
        );

        /**
         * check if the new default media folder id is already set
         * if not, update the media folder id and all related media
         */
        if ((int) $countMediaDefaultFolder === 0) {

            /**
             * fetch the original media folder data
             */
            $originalMediaFolder = $connection->fetchAssociative('
                SELECT mf.* 
                FROM media_folder mf
                JOIN media_default_folder mdf ON mf.default_folder_id = mdf.id
                WHERE mdf.entity = :entity
            ', [
                'entity' => 'blur_elysium_slides',
            ]);

            /**
             * find all media ids by the originnal media folder id
             */
            $mediaIds = $connection->fetchFirstColumn(
                '
                SELECT id 
                FROM media
                WHERE media_folder_id = :mediaFolderId
            ',
                [
                    'mediaFolderId' => $originalMediaFolder['id'],
                ]
            );

            /** 
             * first, delete the original media folder by the original media folder id
             * then change the id in the original media folder data to the new default media folder id
             * and insert the new data into the media_folder table
             */
            $connection->delete('media_folder', [
                'id' => $originalMediaFolder['id'],
            ]);
            $originalMediaFolder['id'] = hex2bin(Defaults::MEDIA_FOLDER_ID);
            $connection->insert('media_folder', $originalMediaFolder);

            if (count($mediaIds) > 0) {
                $connection->executeStatement(
                    'UPDATE media SET media_folder_id = :mediaFolderId WHERE id IN (:mediaIds)',
                    [
                        'mediaFolderId' => hex2bin(Defaults::MEDIA_FOLDER_ID),
                        'mediaIds' => $mediaIds,
                    ],
                    [
                        'mediaIds' => ArrayParameterType::STRING,
                    ]
                );
            }
        }
    }
}
