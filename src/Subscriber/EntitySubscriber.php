<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Blur\BlurElysiumSlider\Defaults;

class EntitySubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly EntityRepository $cmsSectionRepository
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            EntityWrittenContainerEvent::class => 'onEntityWritten',
        ];
    }

    public function onEntityWritten(EntityWrittenContainerEvent $event): void
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
}
