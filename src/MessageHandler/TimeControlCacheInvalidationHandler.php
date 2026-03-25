<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\MessageHandler;

use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\DateTimeParser;
use Blur\BlurElysiumSlider\Service\ElysiumCmsPageLookup;
use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeControlCacheInvalidationHandler
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly ElysiumCmsPageLookup $cmsPageLookup,
        private readonly Connection $connection,
        private readonly DateTimeParser $dateTimeParser
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

        $invalidationTimeUtc = \DateTimeImmutable::createFromInterface($invalidationTime)
            ->setTimezone(new \DateTimeZone('UTC'));
        $invalidationTimeString = $invalidationTimeUtc->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        if ($activeFrom !== null) {
            $activeFromTime = $this->dateTimeParser->parseFromStorage($activeFrom);
            if ($activeFromTime && $activeFromTime->format(Defaults::STORAGE_DATE_TIME_FORMAT) === $invalidationTimeString) {
                return true;
            }
        }

        if ($activeUntil !== null) {
            $activeUntilTime = $this->dateTimeParser->parseFromStorage($activeUntil);
            if ($activeUntilTime && $activeUntilTime->format(Defaults::STORAGE_DATE_TIME_FORMAT) === $invalidationTimeString) {
                return true;
            }
        }

        return false;
    }
}
