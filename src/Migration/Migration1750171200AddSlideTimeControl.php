<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1750171200AddSlideTimeControl extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1750171200;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `active_from` DATETIME(3) NULL
            ');
        } catch (\Throwable $e) {
            if (!($e instanceof NonUniqueFieldNameException)) {
                throw $e;
            }
        }

        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `active_until` DATETIME(3) NULL
            ');
        } catch (\Throwable $e) {
            if (!($e instanceof NonUniqueFieldNameException)) {
                throw $e;
            }
        }
    }
}
