<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Shopware\Core\Content\Media\Event\UnusedMediaSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnusedMediaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UnusedMediaSearchEvent::class => 'removeUsedMedia',
        ];
    }

    public function removeUsedMedia(UnusedMediaSearchEvent $event): void
    {
        /**
         * This event is only triggered on CLI use
         */
        #dd('meddl');
        $idsToBeDeleted = $event->getUnusedIds();

        $doNotDeleteTheseIds = $this->getUsedMediaIds($idsToBeDeleted);

        $event->markAsUsed($doNotDeleteTheseIds);
    }

    private function getUsedMediaIds(array $idsToBeDeleted): array
    {
        return [];
    }
}
