<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1635435811AddCustomFieldsTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1635435811;
    }

    public function update(Connection $connection): void
    {
        try {
            $sql = <<<SQL
                ALTER TABLE `blur_elysium_slides_translation`
                ADD COLUMN `custom_fields` json DEFAULT NULL
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
