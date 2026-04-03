<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Service\TimeControlCacheInvalidationScheduler;
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

        $entities = array_keys(TimeControlCacheInvalidationScheduler::entityConfig());

        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entityName) {
            $this->handleEntity($event, $entityName);
        }
    }

    private function handleEntity(EntityWrittenContainerEvent $event, string $entityName): void
    {
        $entityEvent = $event->getEventByEntityName($entityName);

        /**
         * @todo the schedule method gets executed on **every** CMS section, it ignores the cms section type right now.
         * - It should only be executed if the type of a cms section entity is 'blur-elysium-section'
         */
        if (!$entityEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($entityEvent->getWriteResults() as $writeResult) {
            $this->scheduler->schedule($writeResult, $entityName);
        }
    }
}
