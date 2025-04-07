<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706795531AddSlideProduct extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706795531;
    }

    public function update(Connection $connection): void
    {
        try {
            $sql = <<<SQL
            ALTER TABLE `blur_elysium_slides`
            ADD COLUMN `product_id` BINARY(16) NULL,
            ADD CONSTRAINT `fk.blur_elysium_slides.product_id` FOREIGN KEY (`product_id`)
            REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    SQL;
            $connection->executeStatement($sql);
        } catch (\Exception $e) {
            if (!preg_match('/duplicate column|column exists|Duplicate key/i', $e->getMessage())) {
                throw $e;
            }
        }
    }
}
