<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1645716876AddSlideSettings extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1645716876;
    }

    public function update(Connection $connection): void
    {

        try {
            // implement update
            $sql = <<<SQL
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `slide_settings` JSON NULL
    SQL;

            // add custom field column
            $connection->executeStatement($sql);
        } catch (\Exception $e) {
            if (!preg_match('/duplicate column|column exists/i', $e->getMessage())) {
                throw $e;
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
