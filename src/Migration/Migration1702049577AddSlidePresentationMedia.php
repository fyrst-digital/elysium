<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
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
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `presentation_media_id` BINARY(16) NULL,
                ADD CONSTRAINT `fk.blur_elysium_slides.presentation_media_id` FOREIGN KEY (`presentation_media_id`)
                REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ');
        } catch (\Exception $e) {
            if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }
}
