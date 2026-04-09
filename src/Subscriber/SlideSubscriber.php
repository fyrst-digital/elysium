<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesCriteriaEvent;
use Blur\BlurElysiumSlider\Service\CacheInvalidationScheduler;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SlideSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly CacheInvalidationScheduler $scheduler,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ElysiumSlidesCriteriaEvent::class => 'slideTimeControlFiltering',
            EntityWrittenContainerEvent::class => 'onSlideWritten',
        ];
    }

    public function slideTimeControlFiltering(ElysiumSlidesCriteriaEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $criteria = $event->getCriteria();
        $now = $this->clock->now()->setTimezone(new \DateTimeZone('UTC'));

        $activeFromCondition = new RangeFilter('activeFrom', [
            RangeFilter::LTE => $now->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $activeUntilCondition = new RangeFilter('activeUntil', [
            RangeFilter::GTE => $now->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $activeFromFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('activeFrom', null),
            $activeFromCondition,
        ]);

        $activeUntilFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('activeUntil', null),
            $activeUntilCondition,
        ]);

        $timeFilter = new MultiFilter(MultiFilter::CONNECTION_AND, [
            $activeFromFilter,
            $activeUntilFilter,
        ]);

        $criteria->addFilter($timeFilter);
    }

    public function onSlideWritten(EntityWrittenContainerEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $entityEvent = $event->getEventByEntityName(ElysiumSlidesDefinition::ENTITY_NAME);

        if (!$entityEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($entityEvent->getWriteResults() as $writeResult) {
            $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);
        }
    }
}
