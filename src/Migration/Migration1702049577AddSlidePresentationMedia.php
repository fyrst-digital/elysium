<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1702049577AddSlidePresentationMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702049577;
    }

    public function update(Connection $connection): void
    {
        try {
            $sql = <<<SQL
            ALTER TABLE `blur_elysium_slides`
            ADD COLUMN `presentation_media_id` BINARY(16) NULL,
            ADD CONSTRAINT `fk.blur_elysium_slides.presentation_media_id` FOREIGN KEY (`presentation_media_id`)
            REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    SQL;
            $connection->executeStatement($sql);
        } catch (\Exception $e) {
            if (!preg_match('/duplicate column|column exists|Duplicate key/i', $e->getMessage())) {
                throw $e;
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
