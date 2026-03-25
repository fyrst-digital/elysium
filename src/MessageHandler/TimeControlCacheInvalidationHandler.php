<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeControlCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly ElysiumCmsPageLookup $cmsPageLookup,
        private readonly Connection $connection
    ) {}

    public function __invoke(TimeControlCacheInvalidationMessage $message): void
    {
        $slideId = $message->getSlideId();
        $invalidationTime = $message->getInvalidationTime();

        if (!$this->shouldInvalidate($slideId, $invalidationTime)) {
            return;
        }

        $tags = $this->cmsPageLookup->getCmsCacheTagsBySlideIds($slideId);

        if (!empty($tags)) {
            $this->cacheInvalidator->invalidate($tags);
        }
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
