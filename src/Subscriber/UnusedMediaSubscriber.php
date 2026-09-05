<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Media\Event\UnusedMediaSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnusedMediaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UnusedMediaSearchEvent::class => 'removeUsedMedia',
        ];
    }

    public function removeUsedMedia(UnusedMediaSearchEvent $event): void
    {
        $ids = $event->getUnusedIds();
        if ($ids === []) {
            return;
        }

        $used = $this->connection->fetchFirstColumn(
            'SELECT media_id FROM (
                SELECT JSON_UNQUOTE(JSON_EXTRACT(content_settings, \'$.slideCover.mobileId\')) AS media_id
                FROM blur_elysium_slides_translation
                UNION ALL
                SELECT JSON_UNQUOTE(JSON_EXTRACT(content_settings, \'$.slideCover.tabletId\'))
                FROM blur_elysium_slides_translation
                UNION ALL
                SELECT JSON_UNQUOTE(JSON_EXTRACT(content_settings, \'$.slideCover.desktopId\'))
                FROM blur_elysium_slides_translation
                UNION ALL
                SELECT JSON_UNQUOTE(JSON_EXTRACT(content_settings, \'$.slideCover.videoId\'))
                FROM blur_elysium_slides_translation
                UNION ALL
                SELECT JSON_UNQUOTE(JSON_EXTRACT(content_settings, \'$.focusImageId\'))
                FROM blur_elysium_slides_translation
            ) AS used
            WHERE media_id IN (:ids)',
            ['ids' => $ids],
            ['ids' => ArrayParameterType::STRING]
        );

        $event->markAsUsed(array_values(array_filter($used, static fn (mixed $id): bool => \is_string($id) && $id !== '')));
    }
}
