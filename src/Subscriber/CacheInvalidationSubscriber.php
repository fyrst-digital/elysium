<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

class CacheInvalidationSubscriber
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly ElysiumCmsPageLookup $cmsPageLookup,
    ) {}

    public function invalidateCmsPageIds(EntityWrittenContainerEvent $event): void
    {
        $slideIds = $event->getPrimaryKeys(ElysiumSlidesDefinition::ENTITY_NAME);

        if (empty($slideIds)) {
            return;
        }

        $tags = $this->cmsPageLookup->getCmsCacheTagsBySlideIds($slideIds);

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags);
        }
    }
}
