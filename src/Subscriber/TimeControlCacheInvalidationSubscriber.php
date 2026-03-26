<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\DateTimeParser;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class TimeControlCacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ClockInterface $clock,
        private readonly DateTimeParser $dateTimeParser
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'onSlideWritten',
        ];
    }

    public function onSlideWritten(EntityWrittenContainerEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $slideEvent = $event->getEventByEntityName(ElysiumSlidesDefinition::ENTITY_NAME);

        if (!$slideEvent instanceof EntityWrittenEvent) {
            return;
        }

        foreach ($slideEvent->getWriteResults() as $writeResult) {
            $this->handleWriteResult($writeResult);
        }
    }

    private function handleWriteResult(EntityWriteResult $writeResult): void
    {
        $payload = $writeResult->getPayload();
        $slideId = $writeResult->getProperty('id');

        if ($slideId === null) {
            return;
        }

        $this->scheduleInvalidationForField($slideId, 'active_from', $payload);
        $this->scheduleInvalidationForField($slideId, 'active_until', $payload);
    }

    private function scheduleInvalidationForField(string $slideId, string $fieldName, array $payload): void
    {
        if (!isset($payload[$fieldName])) {
            return;
        }

        $value = $payload[$fieldName];

        if ($value instanceof \DateTimeInterface) {
            $datetime = \DateTimeImmutable::createFromInterface($value);
        } else {
            $datetime = $this->dateTimeParser->parseFromStorage($value);
        }

        if ($datetime === null) {
            return;
        }

        $datetimeUtc = $datetime->setTimezone(new \DateTimeZone('UTC'));
        $now = $this->clock->now();
        $delaySeconds = $datetimeUtc->getTimestamp() - $now->getTimestamp();

        if ($delaySeconds <= 0) {
            $this->messageBus->dispatch(
                new TimeControlCacheInvalidationMessage($slideId, $datetimeUtc)
            );
            return;
        }

        $this->messageBus->dispatch(
            (new Envelope(
                new TimeControlCacheInvalidationMessage($slideId, $datetimeUtc)
            ))->with(new DelayStamp($delaySeconds * 1000))
        );
    }
}
