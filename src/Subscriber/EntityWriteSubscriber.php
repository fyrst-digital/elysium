<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;

class EntityWriteSubscriber implements EventSubscriberInterface
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
                        'paddingX' => 0
                    ],
                ],
            ]
        ];
    }


    public static function getSubscribedEvents()
    {
        return [
            EntityWriteEvent::class => 'beforeWrite',
        ];
    }

    public function beforeWrite(EntityWriteEvent $event)
    {
        $cmsSections = $event->getCommandsForEntity(CmsSectionDefinition::ENTITY_NAME);

        foreach ($cmsSections as $id => $section) {
            if ($section instanceof InsertCommand && $section->getPayload()['type'] === self::SECTION_NAME) {
                /**
                 * @todo #137 check if elysiumSectionSettings already exists in custom_fielda and merge it with defaults
                 * Do that in EntityWriteEvent event? Doesn't make sense. Do the merging in EntityLoadedEvent event?
                 */
                $section->addPayload('custom_fields', json_encode(self::sectionDefauls()));
            }
        }
    }
}
