<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Service\CacheInvalidationScheduler;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\Cms\Events\CmsPageLoaderCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotEqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotEqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\JsonUpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CmsSubscriber implements EventSubscriberInterface
{
    private const ELYSIUM_BLOCK_TYPES = [
        'blur-elysium-banner',
        'blur-elysium-slider',
    ];

    public function __construct(
        private readonly ClockInterface $clock,
        private readonly EntityRepository $cmsSectionRepository,
        private readonly CacheInvalidationScheduler $scheduler,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageLoaderCriteriaEvent::class => 'cmsTimeControlFiltering',
            EntityWriteEvent::class => [
                ['requestSectionChangeSet', 0],
                ['requestBlockChangeSet', 0],
            ],
            EntityWrittenContainerEvent::class => [
                ['initSectionSettings', 0],
                ['scheduleCacheInvalidation', 0],
            ],
        ];
    }

    public function cmsTimeControlFiltering(CmsPageLoaderCriteriaEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $this->filterSections($event);
        $this->filterBlocks($event);
    }

    public function requestSectionChangeSet(EntityWriteEvent $event): void
    {
        $commands = $event->getCommandsForEntity(CmsSectionDefinition::ENTITY_NAME);

        foreach ($commands as $command) {
            if ($command instanceof UpdateCommand) {
                $command->requestChangeSet();
            }
        }
    }

    public function requestBlockChangeSet(EntityWriteEvent $event): void
    {
        $commands = $event->getCommandsForEntity(CmsBlockDefinition::ENTITY_NAME);

        foreach ($commands as $command) {
            if ($command instanceof UpdateCommand) {
                $command->requestChangeSet();
            }
            if ($command instanceof JsonUpdateCommand) {
                $command->requestChangeSet();
            }
        }
    }

    public function initSectionSettings(EntityWrittenContainerEvent $event): void
    {
        /** @var ?EntityWrittenEvent $events */
        $events = $event->getEventByEntityName(CmsSectionDefinition::ENTITY_NAME);
        /** @var ?array<EntityWriteResult> $writeResults */
        $writeResults = $events?->getWriteResults();

        if (is_array($writeResults) && \count($writeResults) > 0) {
            $updates = [];

            foreach ($writeResults as $result) {

                if ($result->getOperation() === EntityWriteResult::OPERATION_INSERT) {
                    $payload = $result->getPayload();

                    if (isset($payload['type']) && $payload['type'] === Defaults::CMS_SECTION_NAME) {
                        $mergedCustomFields = \array_replace_recursive(Defaults::cmsSectionSettings(), isset($payload['custom_fields']) ? $payload['custom_fields'] : []);

                        $updates[] = [
                            'id' => $payload['id'],
                            'customFields' => $mergedCustomFields,
                        ];
                    }
                }
            }

            if (!empty($updates)) {
                $this->cmsSectionRepository->update($updates, $event->getContext());
            }
        }
    }

    public function scheduleCacheInvalidation(EntityWrittenContainerEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        // handle cms sections first to get the affected page ids, then handle blocks with the page ids from the sections
        $pageIds = $this->handleCmsSection($event->getEventByEntityName(CmsSectionDefinition::ENTITY_NAME));
        $this->handleCmsBlock($event->getEventByEntityName(CmsBlockDefinition::ENTITY_NAME), $pageIds);
    }

    private function handleCmsSection(?EntityWrittenEvent $cmsSectionWrittenEvent): array
    {
        $pageIds = [];

        if (!$cmsSectionWrittenEvent instanceof EntityWrittenEvent) {
            return $pageIds;
        }

        foreach ($cmsSectionWrittenEvent->getWriteResults() as $writeResult) {
            $changeSet = $writeResult->getChangeSet();
            $payload = $writeResult->getPayload();
            $entityName = $writeResult->getEntityName();

            if ($changeSet === null || $changeSet->getBefore('type') !== Defaults::CMS_SECTION_NAME) {
                continue;
            }

            if (isset($payload['pageId'])) {
                $pageIds[] = $payload['pageId'];
            }

            $this->scheduler->schedule(
                $payload, 
                $entityName, 
                isset($payload['pageId']) ? [$payload['pageId']] : []
            );
        }
        return $pageIds;
    }

    private function handleCmsBlock(?EntityWrittenEvent $cmsBlockWrittenEvent, array $pageIds): void
    {
        if (!$cmsBlockWrittenEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($cmsBlockWrittenEvent->getWriteResults() as $writeResult) {
            $changeSet = $writeResult->getChangeSet();
            $payload = $writeResult->getPayload();
            $entityName = $writeResult->getEntityName();

            if ($changeSet === null || !in_array($changeSet->getBefore('type'), ['blur-elysium-slider', 'blur-elysium-block'])) {
                continue;
            }

            $this->scheduler->schedule(
                $payload, 
                $entityName, 
                $pageIds
            );
        }
    }

    private function filterSections(CmsPageLoaderCriteriaEvent $event): void
    {
        $nowString = $this->now();

        $settingsKey = Defaults::CMS_SECTION_SETTINGS_KEY;

        // Time filters: (activeFrom IS NULL OR activeFrom <= NOW)
        $activeFromFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.' . $settingsKey . '.activeFrom', null),
            new RangeFilter('customFields.' . $settingsKey . '.activeFrom', [
                RangeFilter::LTE => $nowString,
            ]),
        ]);

        // Time filters: (activeUntil IS NULL OR activeUntil >= NOW)
        $activeUntilFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.' . $settingsKey . '.activeUntil', null),
            new RangeFilter('customFields.' . $settingsKey . '.activeUntil', [
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

        $event->getCriteria()->getAssociation('sections')->addFilter($finalFilter);
    }

    private function filterBlocks(CmsPageLoaderCriteriaEvent $event): void
    {
        $nowString = $this->now();

        $settingsKey = Defaults::CMS_BLOCK_ADVANCED_KEY;

        // Time filters: (activeFrom IS NULL OR activeFrom <= NOW)
        $activeFromFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.' . $settingsKey . '.activeFrom', null),
            new EqualsFilter('customFields.' . $settingsKey . '.activeFrom', ''),
            new RangeFilter('customFields.' . $settingsKey . '.activeFrom', [
                RangeFilter::LTE => $nowString,
            ]),
        ]);

        // Time filters: (activeUntil IS NULL OR activeUntil >= NOW)
        $activeUntilFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customFields.' . $settingsKey . '.activeUntil', null),
            new EqualsFilter('customFields.' . $settingsKey . '.activeUntil', ''),
            new RangeFilter('customFields.' . $settingsKey . '.activeUntil', [
                RangeFilter::GTE => $nowString,
            ]),
        ]);

        // Valid elysium block: type is elysium AND time filters pass
        $validElysiumBlock = new MultiFilter(MultiFilter::CONNECTION_AND, [
            new EqualsAnyFilter('type', self::ELYSIUM_BLOCK_TYPES),
            $activeFromFilter,
            $activeUntilFilter,
        ]);

        // Include: non-elysium blocks OR valid elysium blocks
        $finalFilter = new MultiFilter(MultiFilter::CONNECTION_OR, [
            new NotEqualsAnyFilter('type', self::ELYSIUM_BLOCK_TYPES),
            $validElysiumBlock,
        ]);

        $event->getCriteria()->getAssociation('sections.blocks')->addFilter($finalFilter);
    }

    private function now(): string
    {
        return $this->clock->now()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.v\Z');
    }
}
