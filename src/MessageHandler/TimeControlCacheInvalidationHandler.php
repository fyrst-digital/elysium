<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeControlCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly ElysiumCmsPageLookup $cmsPageLookup
    ) {}

    public function __invoke(TimeControlCacheInvalidationMessage $message): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $entityId = $message->getEntityId();

        $tags = match ($message->getEntityName()) {
            ElysiumSlidesDefinition::ENTITY_NAME => $this->cmsPageLookup->getCmsCacheTagsBySlideIds([$entityId]),
            CmsSectionDefinition::ENTITY_NAME => $this->cmsPageLookup->getCmsCacheTagsBySectionIds([$entityId]),
        };

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags, true);
        }
    }
}
