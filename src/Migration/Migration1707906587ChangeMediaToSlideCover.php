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
        // implement update
        $sql = <<<SQL
        ALTER TABLE `blur_elysium_slides`
        CHANGE `media_id` `slide_cover_id` BINARY(16) NULL,
        CHANGE `media_portrait_id` `slide_cover_mobile_id` BINARY(16) NULL;
SQL;

        // add custom field column
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
