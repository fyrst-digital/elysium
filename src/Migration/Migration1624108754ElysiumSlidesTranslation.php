<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624108754ElysiumSlidesTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1624108754;
    }

    public function update(Connection $connection): void
    {
        // implement update
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `blur_elysium_slides_translation` (
            `blur_elysium_slides_id` BINARY(16) NOT NULL,
            `language_id` BINARY(16) NOT NULL,
            `name` VARCHAR(255),
            `title` VARCHAR(255),
            `description` LONGTEXT,
            `button_label` VARCHAR(255),
            `url` LONGTEXT,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`blur_elysium_slides_id`, `language_id`),
            CONSTRAINT `fk.blur_elysium_slides_translation.blur_elysium_slides_id` FOREIGN KEY (`blur_elysium_slides_id`)
                REFERENCES `blur_elysium_slides` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.blur_elysium_slides_translation.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci
SQL;

        $connection->executeStatement($sql);
    }
}
