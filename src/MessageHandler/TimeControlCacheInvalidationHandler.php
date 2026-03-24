<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeControlCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly Connection $connection
    ) {}

    public function __invoke(TimeControlCacheInvalidationMessage $message): void
    {
        $slideId = $message->getSlideId();
        $invalidationTime = $message->getInvalidationTime();

        if (!$this->shouldInvalidate($slideId, $invalidationTime)) {
            return;
        }

        $cmsPageIds = $this->connection->fetchFirstColumn(
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

        if (empty($cmsPageIds)) {
            return;
        }

        $cmsPageIds = array_unique($cmsPageIds);
        $tags = array_map(EntityCacheKeyGenerator::buildCmsTag(...), $cmsPageIds);

        $this->cacheInvalidator->invalidate($tags);
    }

    private function shouldInvalidate(string $slideId, \DateTimeInterface $invalidationTime): bool
    {
        $slideData = $this->connection->fetchAssociative(
            "SELECT active_from, active_until FROM blur_elysium_slides WHERE id = UNHEX(:slideId)",
            ['slideId' => $slideId]
        );

        if ($slideData === false) {
            return false;
        }

        $activeFrom = $slideData['active_from'];
        $activeUntil = $slideData['active_until'];

        if ($activeFrom === null && $activeUntil === null) {
            return false;
        }

        $invalidationTimeString = $invalidationTime->format('Y-m-d H:i:s');

        if ($activeFrom !== null) {
            $activeFromTime = substr($activeFrom, 0, 19);
            if ($activeFromTime === $invalidationTimeString) {
                return true;
            }
        }

        if ($activeUntil !== null) {
            $activeUntilTime = substr($activeUntil, 0, 19);
            if ($activeUntilTime === $invalidationTimeString) {
                return true;
            }
        }

        return false;
    }
}
