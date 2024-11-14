<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Cms\Events\CmsPageLoadedEvent;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;

class CmsSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            CmsPageLoadedEvent::class => 'cmsPageLoaded'
        ];
    }

    public function cmsPageLoaded(CmsPageLoadedEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $cmsPage = $event->getResult()->first();
        /** @var CmsSectionCollection */
        $cmsSections = $cmsPage?->getSections();
        $filteredCmsSections = $cmsSections->filter(function ($section) {
            return $section->getType() === 'blur-elysium-section';
        });

        if ($filteredCmsSections->count() > 0) {
            $salesChannelContext->addArrayExtension('elysiumSection', ['hasSections' => true]);
        }
    }
}
