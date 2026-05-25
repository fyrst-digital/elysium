<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1780000000AddSlideCategory extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1780000000;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `category_id` BINARY(16) NULL,
                ADD COLUMN `category_version_id` BINARY(16) NULL,
                ADD CONSTRAINT `fk.blur_elysium_slides.category_id` FOREIGN KEY (`category_id`, `category_version_id`)
                REFERENCES `category` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE
            ');
        } catch (\Exception $e) {
            if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }
}
