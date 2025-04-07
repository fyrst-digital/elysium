<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
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
            $connection->executeStatement("
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `slide_settings` JSON NULL
            ");
        } catch (\Throwable $e) {
            if (!($e instanceof NonUniqueFieldNameException)) {
                throw $e;
            }
        }
    }
}
