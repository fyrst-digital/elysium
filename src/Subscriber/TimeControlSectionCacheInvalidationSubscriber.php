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
        $sectionId = $writeResult->getProperty('id');

        if ($sectionId === null) {
            return;
        }

        $this->scheduleInvalidation($sectionId, 'activeFrom', $payload);
        $this->scheduleInvalidation($sectionId, 'activeUntil', $payload);
    }

    private function scheduleInvalidation(string $sectionId, string $fieldName, array $payload): void
    {
        if (!isset($payload['custom_fields'][Defaults::CMS_SECTION_SETTINGS_KEY][$fieldName])) {
            return;
        }

        $value = $payload['custom_fields'][Defaults::CMS_SECTION_SETTINGS_KEY][$fieldName];

        if ($value === null) {
            return;
        }

        if ($value instanceof \DateTimeInterface) {
            $datetime = \DateTimeImmutable::createFromInterface($value);
        } else {
            $datetime = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $value
            );
        }

        if ($datetime === false) {
            return;
        }

        $now = $this->clock->now();
        $delaySeconds = $datetime->getTimestamp() - $now->getTimestamp();

        if ($delaySeconds <= 0) {
            $this->messageBus->dispatch(
                new TimeControlSectionCacheInvalidationMessage($sectionId, $datetime)
            );
            return;
        }

        $this->messageBus->dispatch(
            (new Envelope(
                new TimeControlSectionCacheInvalidationMessage($sectionId, $datetime)
            ))->with(new DelayStamp($delaySeconds * 1000))
        );
    }
}
