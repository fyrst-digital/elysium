<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
use Shopware\Core\Content\Cms\Events\CmsPageLoaderCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotEqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionTimeControlSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageLoaderCriteriaEvent::class => 'cmsPageLoaderCriteria',
        ];
    }

    public function cmsPageLoaderCriteria(CmsPageLoaderCriteriaEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $criteria = $event->getCriteria();
        $now = $this->clock->now()->setTimezone(new \DateTimeZone('UTC'));
        
        // Match ISO 8601 format stored in customFields JSON
        $nowString = $now->format('Y-m-d\TH:i:s.v\Z');

        // Time filters: (activeFrom IS NULL OR activeFrom <= NOW)
        $activeFromFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.elysiumSectionSettings.activeFrom', null),
            new RangeFilter('customFields.elysiumSectionSettings.activeFrom', [
                RangeFilter::LTE => $nowString,
            ]),
        ]);

        // Time filters: (activeUntil IS NULL OR activeUntil >= NOW)
        $activeUntilFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.elysiumSectionSettings.activeUntil', null),
            new RangeFilter('customFields.elysiumSectionSettings.activeUntil', [
                RangeFilter::GTE => $nowString,
            ]),
        ]);

        // Valid elysium section: type='blur-elysium-section' AND time filters pass
        $validElysiumSection = new MultiFilter(MultiFilter::CONNECTION_AND, [
            new EqualsFilter('type', Defaults::CMS_SECTION_NAME),
            $activeFromFilter,
            $activeUntilFilter,
        ]);

        // Include: non-elysium sections OR valid elysium sections
        $finalFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new NotEqualsFilter('type', Defaults::CMS_SECTION_NAME),
            $validElysiumSection,
        ]);

        $criteria->getAssociation('sections')->addFilter($finalFilter);
    }
}