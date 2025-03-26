<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1707906587ChangeMediaToSlideCover extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1707906587;
    }

    public function update(Connection $connection): void
    {
        /**
         * @deprecated 
         * In version 4 there will be only the RENAME COLUMN syntax with no excuses!
         * ALTER TABLE `blur_elysium_slides`
         * RENAME COLUMN `media_id` TO `slide_cover_id`,
         * RENAME COLUMN `media_portrait_id` TO `slide_cover_mobile_id`
         */

        try {
            $sql = <<<SQL
            ALTER TABLE `blur_elysium_slides`
            CHANGE `media_id` `slide_cover_id` BINARY(16) NULL,
            CHANGE `media_portrait_id` `slide_cover_mobile_id` BINARY(16) NULL;
    SQL;
            $connection->executeStatement($sql);
        } catch (\Exception $e) {
            if (!preg_match('/duplicate column|column exists|unknown column/i', $e->getMessage())) {
                throw $e;
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
