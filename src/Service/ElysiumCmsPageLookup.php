<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;

class ElysiumCmsPageLookup
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @param string[] $slideIds Multiple slide IDs (hex)
     * @return string[] Cache tags for CMS pages
     */
    public function getCmsCacheTagsBySlideIds(array $slideIds): array
    {
        if (empty($slideIds)) {
            return [];
        }

        $cmsPageIds = $this->connection->fetchFirstColumn(
            "SELECT DISTINCT LOWER(HEX(cms_section.cms_page_id)) AS cms_page_id
            FROM cms_slot
            JOIN cms_slot_translation ON cms_slot_translation.cms_slot_id = cms_slot.id
            JOIN cms_block ON cms_block.id = cms_slot.cms_block_id
            JOIN cms_section ON cms_section.id = cms_block.cms_section_id
            WHERE cms_slot.type = 'blur-elysium-banner'
              AND JSON_UNQUOTE(JSON_EXTRACT(cms_slot_translation.config, '$.elysiumSlide.value')) IN (:slideIds)

            UNION

            SELECT DISTINCT LOWER(HEX(cms_section.cms_page_id)) AS cms_page_id
            FROM cms_slot
            JOIN cms_slot_translation ON cms_slot_translation.cms_slot_id = cms_slot.id
            JOIN cms_block ON cms_block.id = cms_slot.cms_block_id
            JOIN cms_section ON cms_section.id = cms_block.cms_section_id
            WHERE cms_slot.type = 'blur-elysium-slider'
              AND EXISTS (
                  SELECT 1 FROM JSON_TABLE(
                      JSON_EXTRACT(cms_slot_translation.config, '$.elysiumSlideCollection.value'),
                      '$[*]' COLUMNS (slide_id VARCHAR(32) PATH '$')
                  ) AS jt
                  WHERE jt.slide_id IN (:slideIds)
              )",
            ['slideIds' => $slideIds],
            ['slideIds' => ArrayParameterType::STRING]
        );

        $cmsPageIds = array_unique($cmsPageIds);

        if (empty($cmsPageIds)) {
            return [];
        }

        return array_map(EntityCacheKeyGenerator::buildCmsTag(...), $cmsPageIds);
    }
}
