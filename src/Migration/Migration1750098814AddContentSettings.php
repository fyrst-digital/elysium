<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1750098814AddContentSettings extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1750098814;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides_translation`
                ADD COLUMN `content_settings` JSON NULL
            ');
        } catch (\Throwable $e) {
            if (!($e instanceof NonUniqueFieldNameException)) {
                throw $e;
            }
        }
    }
}
