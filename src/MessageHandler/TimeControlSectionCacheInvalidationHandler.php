<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Message\TimeControlSectionCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeControlSectionCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly ElysiumCmsPageLookup $cmsPageLookup
    ) {}

    public function __invoke(TimeControlSectionCacheInvalidationMessage $message): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $sectionId = $message->getSectionId();

        $tags = $this->cmsPageLookup->getCmsCacheTagsBySectionIds([$sectionId]);

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags, true);
        }
    }
}
