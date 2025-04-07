<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Blur\BlurElysiumSlider\Defaults;

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
        $connection->executeStatement("
            UPDATE media_folder mf
            JOIN media_default_folder mdf ON mf.default_folder_id = mdf.id
            SET mf.id = UNHEX(:mediaFolderId)
            WHERE mdf.entity = 'blur_elysium_slides';
        ", [
            'mediaFolderId' => Defaults::MEDIA_FOLDER_ID
        ]);
    }
}
