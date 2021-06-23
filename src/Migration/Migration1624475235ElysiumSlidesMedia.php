<?php declare(strict_types=1);

namespace Blur\ElysiumBlocks\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624475235ElysiumSlidesMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1624475235;
    }

    public function update(Connection $connection): void
    {
        // implement update
        $connection->executeUpdate('
            CREATE TABLE `blur_elysium_slides_media` (
                `id` BINARY(16) NOT NULL,
                `version_id` BINARY(16) NOT NULL,
                `position` INT(11) NOT NULL DEFAULT 1,
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `blur_elysium_slides_version_id` BINARY(16) NOT NULL,
                `media_id` BINARY(16) NOT NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`, `version_id`),
                CONSTRAINT `json.blur_elysium_slides_media.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                CONSTRAINT `fk.blur_elysium_slides_media.media_id` FOREIGN KEY (`media_id`)
                    REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides_media.blur_elysium_slides_id` FOREIGN KEY (`blur_elysium_slides_id`, `blur_elysium_slides_version_id`)
                    REFERENCES `blur_elysium_slides` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
