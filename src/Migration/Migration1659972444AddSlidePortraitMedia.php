<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1659972444AddSlidePortraitMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659972444;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `media_portrait_id` BINARY(16) NULL,
                ADD CONSTRAINT `fk.blur_elysium_slides.media_portrait_id` FOREIGN KEY (`media_portrait_id`)
                REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ');
        } catch (\Throwable $e) {
            if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }
}
