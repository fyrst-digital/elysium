<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap\UpdateMigration;

use Doctrine\DBAL\Connection;

class Update210CmsSlotSliderRemoveSizing
{
    protected const CMS_SLOT_SLIDER_NAME = 'blur-elysium-slider';

    function __construct(
        private readonly Connection $connection
    ) {
    }

    function execute(): void
    {

        $this->connection->executeStatement(
            "UPDATE `cms_slot_translation` 
            LEFT JOIN `cms_slot` ON `cms_slot_translation`.`cms_slot_id` = `cms_slot`.`id`
            SET `config` = JSON_REMOVE(
            `config`, 
            '$.sizing'
            )
            WHERE `cms_slot`.`type` = :element",
            [
                'element' => self::CMS_SLOT_SLIDER_NAME,
            ]
        );
    }
}
