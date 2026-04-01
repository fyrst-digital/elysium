<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Message\TimeControlSectionCacheInvalidationMessage;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Shopware\Core\Framework\Uuid\Uuid;

class TimeControlSectionCacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ClockInterface $clock
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'onSectionWritten',
        ];
    }

    public function onSectionWritten(EntityWrittenContainerEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $sectionEvent = $event->getEventByEntityName(CmsSectionDefinition::ENTITY_NAME);

        if (!$sectionEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($sectionEvent->getWriteResults() as $writeResult) {
            $this->handleWriteResult($writeResult);
        }
    }

    private function handleWriteResult(EntityWriteResult $writeResult): void
    {
        $payload = $writeResult->getPayload();
        $cmsPageId = $writeResult->getProperty('pageId');

        if ($cmsPageId && Uuid::isValid($cmsPageId)) {
            $this->sendInvalidation($cmsPageId, $payload);
        }

    }

    private function sendInvalidation(string $cmsPageId, array $payload): void {

        $activeTimes = [
            'activeFrom' => $payload['customFields'][Defaults::CMS_SECTION_SETTINGS_KEY]['activeFrom'] ?? null,
            'activeUntil' => $payload['customFields'][Defaults::CMS_SECTION_SETTINGS_KEY]['activeUntil'] ?? null,
        ];

        foreach ($activeTimes as $fieldName => $value) {

            if ($value === null) {
                continue;
            }
                
            if ($value instanceof \DateTimeInterface) {
                $datetime = \DateTimeImmutable::createFromInterface($value);
            } else {
                $datetime = new \DateTimeImmutable($value);
            }

            if ($datetime === false) {
                continue;
            }

            
            $now = $this->clock->now();
            $delaySeconds = $datetime->getTimestamp() - $now->getTimestamp();

            if ($delaySeconds <= 0) {
                $this->messageBus->dispatch(
                    new TimeControlSectionCacheInvalidationMessage($cmsPageId, $datetime)
                );
                return;
            }

            $this->messageBus->dispatch(
                (new Envelope(
                    new TimeControlSectionCacheInvalidationMessage($cmsPageId, $datetime)
                ))->with(new DelayStamp($delaySeconds * 1000))
            );
        }
    }
}
