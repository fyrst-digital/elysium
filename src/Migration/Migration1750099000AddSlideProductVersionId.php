<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1750099000AddSlideProductVersionId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1750099000;
    }

    public function update(Connection $connection): void
    {
        $connection->transactional(function (Connection $connection) {
            $this->addProductVersionIdColumn($connection);
            $this->populateProductVersionId($connection);
            $this->updateForeignKeyConstraint($connection);
        });
    }

    private function addProductVersionIdColumn(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD COLUMN `product_version_id` BINARY(16) NULL AFTER `product_id`
            ');
        } catch (\Exception $e) {
            if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }

    private function populateProductVersionId(Connection $connection): void
    {
        $connection->executeStatement('
            UPDATE `blur_elysium_slides` AS es
            INNER JOIN `product` AS p ON p.id = es.product_id
            SET es.product_version_id = p.version_id
            WHERE es.product_id IS NOT NULL
        ');
    }

    private function updateForeignKeyConstraint(Connection $connection): void
    {
        $this->dropOldForeignKey($connection);
        $this->addNewForeignKey($connection);
    }

    private function dropOldForeignKey(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                DROP FOREIGN KEY `fk.blur_elysium_slides.product_id`
            ');
        } catch (\Exception $e) {
            if (!preg_match('/Cannot drop foreign key/', $e->getMessage())) {
                throw $e;
            }
        }
    }

    private function addNewForeignKey(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides`
                ADD CONSTRAINT `fk.blur_elysium_slides.product_id`
                FOREIGN KEY (`product_id`, `product_version_id`)
                REFERENCES `product` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE
            ');
        } catch (\Exception $e) {
            if (!preg_match('/Duplicate key name/', $e->getMessage())) {
                throw $e;
            }
        }
    }
}