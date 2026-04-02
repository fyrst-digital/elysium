<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Service\TimeControlCacheInvalidationScheduler;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\Feature;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeControlCacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TimeControlCacheInvalidationScheduler $scheduler
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'onEntityWritten',
        ];
    }

    public function onEntityWritten(EntityWrittenContainerEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $entities = [
            ElysiumSlidesDefinition::ENTITY_NAME,
            CmsSectionDefinition::ENTITY_NAME,
        ];

        foreach ($entities as $entityName) {
            $this->handleEntity($event, $entityName);
        }
    }

    private function handleEntity(EntityWrittenContainerEvent $event, string $entityName): void
    {
        $entityEvent = $event->getEventByEntityName($entityName);

        if (!$entityEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($entityEvent->getWriteResults() as $writeResult) {
            $this->scheduler->schedule($writeResult, $entityName);
        }
    }
}
