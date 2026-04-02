<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class TimeControlCacheInvalidationScheduler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ClockInterface $clock
    ) {}

    public function schedule(EntityWriteResult $writeResult, string $entityName): void
    {
        $id = $this->extractId($writeResult, $entityName);

        if ($id === null) {
            return;
        }

        $payload = $writeResult->getPayload();
        $activeTimes = $this->extractActiveTimes($payload, $entityName);

        foreach ($activeTimes as $value) {
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

            $message = new TimeControlCacheInvalidationMessage($id, $entityName, $datetime);

            if ($delaySeconds <= 0) {
                $this->messageBus->dispatch($message);
                return;
            }

            $this->messageBus->dispatch(
                (new Envelope($message))->with(new DelayStamp($delaySeconds * 1000))
            );
        }
    }

    private function extractId(EntityWriteResult $writeResult, string $entityName): ?string
    {
        if ($entityName === ElysiumSlidesDefinition::ENTITY_NAME) {
            return $writeResult->getProperty('id');
        }

        $id = $writeResult->getProperty('pageId');

        if ($id !== null && !Uuid::isValid($id)) {
            return null;
        }

        return $id;
    }

    private function extractActiveTimes(array $payload, string $entityName): array
    {
        if ($entityName === ElysiumSlidesDefinition::ENTITY_NAME) {
            return [
                'activeFrom' => $payload['activeFrom'] ?? null,
                'activeUntil' => $payload['activeUntil'] ?? null,
            ];
        }

        return [
            'activeFrom' => $payload['customFields'][Defaults::CMS_SECTION_SETTINGS_KEY]['activeFrom'] ?? null,
            'activeUntil' => $payload['customFields'][Defaults::CMS_SECTION_SETTINGS_KEY]['activeUntil'] ?? null,
        ];
    }
}
