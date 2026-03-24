<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumCmsSlidesCriteriaEvent;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesCriteriaEvent;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeControlSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ElysiumSlidesCriteriaEvent::class => 'onSlidesCriteria',
            ElysiumCmsSlidesCriteriaEvent::class => 'onSlidesCriteria',
        ];
    }

    public function onSlidesCriteria(ElysiumSlidesCriteriaEvent|ElysiumCmsSlidesCriteriaEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $criteria = $event->getCriteria();
        $now = new \DateTimeImmutable();

        $activeFromCondition = new RangeFilter('activeFrom', [
            RangeFilter::LTE => $now->format('Y-m-d H:i:s'),
        ]);

        $activeUntilCondition = new RangeFilter('activeUntil', [
            RangeFilter::GTE => $now->format('Y-m-d H:i:s'),
        ]);

        $activeFromFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('activeFrom', null),
            $activeFromCondition,
        ]);

        $activeUntilFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('activeUntil', null),
            $activeUntilCondition,
        ]);

        $timeFilter = new MultiFilter(MultiFilter::CONNECTION_AND, [
            $activeFromFilter,
            $activeUntilFilter,
        ]);

        $criteria->addFilter($timeFilter);
    }
}
