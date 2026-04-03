<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
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

        if (!$entityEvent instanceof EntityWrittenEvent) {
            return;
        }
        
        foreach ($entityEvent->getWriteResults() as $writeResult) {
            $changeSet = $writeResult->getChangeSet();
            if ($entityName === CmsSectionDefinition::ENTITY_NAME && $changeSet?->getBefore('type') !== Defaults::CMS_SECTION_NAME) {
                // Skip if a cms section type is not 'blur-elysium-section'
                continue;
            }
            $this->scheduler->schedule($writeResult, $entityName);
        }
    }
}
