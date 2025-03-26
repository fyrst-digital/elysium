<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624100471ElysiumSlides extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1624100471;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `blur_elysium_slides` (
            `id` BINARY(16) NOT NULL,
            `translations` BINARY(16) NULL,
            `media_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3),
            PRIMARY KEY (`id`),
            CONSTRAINT `fk.blur_elysium_slides.media_id` FOREIGN KEY (`media_id`)
                REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
        $sql = <<<SQL
            DROP TABLE `blur_elysium_slides_translation`, `blur_elysium_slides`
SQL;

        $connection->executeStatement($sql);
    }
}
