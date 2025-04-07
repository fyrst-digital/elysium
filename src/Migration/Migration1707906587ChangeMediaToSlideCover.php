<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Blur\BlurElysiumSlider\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1707906587ChangeMediaToSlideCover extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1707906587;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement("
                ALTER TABLE `blur_elysium_slides`
                RENAME COLUMN `media_id` TO `slide_cover_id`,
                RENAME COLUMN `media_portrait_id` TO `slide_cover_mobile_id`
            ");
        } catch (\Exception $e) {
            if (!preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }
}
