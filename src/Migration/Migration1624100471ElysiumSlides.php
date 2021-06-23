<?php declare(strict_types=1);

namespace Blur\ElysiumBlocks\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624100471ElysiumSlides extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1624100471;
    }

    public function update( Connection $connection ): void
    {
        // implement update
        /**
         * First step, create translation
         * 
         * For further steps:
         * `translations` BINARY(16),
         * `customFieldSets` BINARY(16) NULL,
         */
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `blur_elysium_slides` (
            `id` BINARY(16) NOT NULL,
            `version_id` BINARY(16) NOT NULL,
            `translations` BINARY(16) NULL,
            `blur_elysium_slides_media_id` BINARY(16) NULL,
            `blur_elysium_slides_media_version_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3),
            PRIMARY KEY (`id`, `version_id`)
        )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        SQL;
        
        $connection->executeStatement($sql);
    }

    public function updateDestructive( Connection $connection ): void
    {
        // implement update destructive
    }
}
