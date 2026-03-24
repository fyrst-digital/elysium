<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

class CacheInvalidationSubscriber
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly Connection $connection,
    ) {}

    public function invalidateCmsPageIds(EntityWrittenContainerEvent $event): void
    {
        $slideIds = $event->getPrimaryKeys(ElysiumSlidesDefinition::ENTITY_NAME);

        if (empty($slideIds)) {
            return;
        }

        $cmsPageIds = [];

        foreach ($slideIds as $slideId) {
            $pageIds = $this->connection->fetchFirstColumn(
                "SELECT DISTINCT LOWER(HEX(cms_section.cms_page_id)) AS cms_page_id
                FROM cms_slot
                JOIN cms_slot_translation ON cms_slot_translation.cms_slot_id = cms_slot.id
                JOIN cms_block ON cms_block.id = cms_slot.cms_block_id
                JOIN cms_section ON cms_section.id = cms_block.cms_section_id
                WHERE cms_slot.type = 'blur-elysium-banner'
                  AND JSON_UNQUOTE(JSON_EXTRACT(cms_slot_translation.config, '$.elysiumSlide.value')) = :slideId

                UNION

                SELECT DISTINCT LOWER(HEX(cms_section.cms_page_id)) AS cms_page_id
                FROM cms_slot
                JOIN cms_slot_translation ON cms_slot_translation.cms_slot_id = cms_slot.id
                JOIN cms_block ON cms_block.id = cms_slot.cms_block_id
                JOIN cms_section ON cms_section.id = cms_block.cms_section_id
                WHERE cms_slot.type = 'blur-elysium-slider'
                  AND JSON_CONTAINS(
                    JSON_EXTRACT(cms_slot_translation.config, '$.elysiumSlideCollection.value'),
                    JSON_QUOTE(:slideId)
                  )",
                ['slideId' => $slideId]
            );

            $cmsPageIds = array_merge($cmsPageIds, $pageIds);
        }

        $cmsPageIds = array_unique($cmsPageIds);

        if (!empty($cmsPageIds)) {
            $ids = array_map(EntityCacheKeyGenerator::buildCmsTag(...), $cmsPageIds);
            $this->cacheInvalidator->invalidate($ids);
        }
    }
}
