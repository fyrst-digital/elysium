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

    /**
     * @todo Find unused Elysium Slide Media (CLI usage only)
     */
    public function removeUsedMedia(UnusedMediaSearchEvent $event): void
    {
    }
}
