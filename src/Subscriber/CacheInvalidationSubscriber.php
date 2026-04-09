<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Service\CmsPageLookup;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

class CacheInvalidationSubscriber
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly CmsPageLookup $cmsPageLookup,
    ) {}

    /**
     * Invalidates CMS page cache tags related to Elysium Slides when slides are created, updated, or deleted.
     * Automaticlly listen to Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent as it is defined inn the services.xml file.
     */
    public function invalidateCmsPageIds(EntityWrittenContainerEvent $event): void
    {
        $slideIds = $event->getPrimaryKeys(ElysiumSlidesDefinition::ENTITY_NAME);

        if (empty($slideIds)) {
            return;
        }

        $tags = $this->cmsPageLookup->getCmsCacheTagsBySlideIds($slideIds);

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags, true);
        }
    }
}
