<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\CmsPageLookup;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;

#[AsMessageHandler]
class TimeControlCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly CmsPageLookup $cmsPageLookup
    ) {}

    public function __invoke(TimeControlCacheInvalidationMessage $message): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $entityId = $message->getEntityId();

        $tags = match ($message->getEntityName()) {
            ElysiumSlidesDefinition::ENTITY_NAME => $this->cmsPageLookup->getCmsCacheTagsBySlideIds([$entityId]), // lookup needed to find affected CMS pages based on slide ID
            CmsSectionDefinition::ENTITY_NAME => [EntityCacheKeyGenerator::buildCmsTag($entityId)], // the cms page id gets passed as entityId, so we can directly generate the tag without a lookup
            CmsBlockDefinition::ENTITY_NAME => [EntityCacheKeyGenerator::buildCmsTag($entityId)], // the cms page id from block payload
            default => [],
        };

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags, true);
        }
    }
}
