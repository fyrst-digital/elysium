<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1709495787AddSlideCoverVideo extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1709495787;
    }

    public function update(Connection $connection): void
    {
        try {
            $sql = <<<SQL
            ALTER TABLE `blur_elysium_slides`
            ADD COLUMN `slide_cover_video_id` BINARY(16) NULL,
            ADD CONSTRAINT `fk.blur_elysium_slides.slide_cover_video_id` FOREIGN KEY (`slide_cover_video_id`)
            REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    SQL;
            $connection->executeStatement($sql);
        } catch (\Exception $e) {
            if (!preg_match('/duplicate column|column exists|unknown column/i', $e->getMessage())) {
                throw $e;
            }
        }
    }
}
