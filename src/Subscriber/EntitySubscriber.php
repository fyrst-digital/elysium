<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;

class EntitySubscriber implements EventSubscriberInterface
{
    const SECTION_NAME = 'blur-elysium-section';

    static public function sectionDefauls(): array
    {
        return [
            'elysiumSectionSettings' => [
                'breakpoints' => [
                    'mobile' => null,
                    'tablet' => null,
                    'desktop' => null
                ],
                'viewports' => [
                    'mobile' => [
                        'gridCols' => 12,
                        'gridGap' => 20,
                        'alignItems' => 'stretch',
                        'paddingY' => 20,
                        'paddingX' => 0
                    ],
                    'tablet' => [
                        'gridCols' => 12,
                        'gridGap' => 40,
                        'alignItems' => 'stretch',
                        'paddingY' => 40,
                        'paddingX' => 0
                    ],
                    'desktop' => [
                        'gridCols' => 12,
                        'gridGap' => 40,
                        'alignItems' => 'stretch',
                        'paddingY' => 40,
                        'paddingX' => 0,
                    ],
                ],
            ]
        ];
    }


    public static function getSubscribedEvents()
    {
        return [
            EntityWriteEvent::class => 'beforeWrite',
            EntityLoadedContainerEvent::class => 'loaded',
        ];
    }

    public function beforeWrite(EntityWriteEvent $event): void
    {
        $cmsSections = $event->getCommandsForEntity(CmsSectionDefinition::ENTITY_NAME);

        foreach ($cmsSections as $id => $section) {
            if ($section instanceof InsertCommand && $section->getPayload()['type'] === self::SECTION_NAME) {
                $section->addPayload('custom_fields', json_encode(self::sectionDefauls()));
            }
        }
    }

    public function loaded(EntityLoadedContainerEvent $event): void
    {
        $events = $event->getEvents();

        foreach ($events as $entity) {
            /** @var EntityLoadedEvent $entity */

            if ($entity->getDefinition() instanceof CmsSectionDefinition) {

                foreach ($entity->getEntities() as $cmsSection) {
                    /** @var CmsSectionEntity $cmsSection */

                    if ($cmsSection->getType() === self::SECTION_NAME) {
                        $mergedSectionSettings = \array_replace_recursive(self::sectionDefauls()['elysiumSectionSettings'], $cmsSection->getCustomFieldsValue('elysiumSectionSettings') ?? []);
                        $cmsSection->setCustomFields(['elysiumSectionSettings' => $mergedSectionSettings]);
                    }
                }
            }
        }
    }
}
