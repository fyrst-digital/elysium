<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Doctrine\DBAL\Connection;
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

        $slideId = $message->getSlideId();

        $tags = $this->cmsPageLookup->getCmsCacheTagsBySlideIds([$slideId]);

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags, true);
        }
    }
}
