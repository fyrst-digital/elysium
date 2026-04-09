<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class CacheInvalidationScheduler
{
    public static function entityConfig(): array
    {
        return [
            ElysiumSlidesDefinition::ENTITY_NAME => [
                'id_field' => 'id',
                'active_from' => 'activeFrom',
                'active_until' => 'activeUntil',
            ],
            CmsSectionDefinition::ENTITY_NAME => [
                'id_field' => 'pageId',
                'active_from' => 'customFields.' . Defaults::CMS_SECTION_SETTINGS_KEY . '.activeFrom',
                'active_until' => 'customFields.' . Defaults::CMS_SECTION_SETTINGS_KEY . '.activeUntil',
            ],
            CmsBlockDefinition::ENTITY_NAME => [
                'id_field' => 'pageId',
                'active_from' => 'customFields.' . Defaults::CMS_BLOCK_ADVANCED_KEY . '.activeFrom',
                'active_until' => 'customFields.' . Defaults::CMS_BLOCK_ADVANCED_KEY . '.activeUntil',
            ],
        ];
    }

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

            $now = $this->clock->now();
            $delaySeconds = $datetime->getTimestamp() - $now->getTimestamp();

            $message = new TimeControlCacheInvalidationMessage($id, $entityName, $datetime);

            if ($delaySeconds > 0) {
                $this->messageBus->dispatch(
                    (new Envelope($message))->with(new DelayStamp($delaySeconds * 1000))
                );
            } else if ($entityName === ElysiumSlidesDefinition::ENTITY_NAME) {
                $this->messageBus->dispatch($message);
            }

        }
    }

    private function extractId(EntityWriteResult $writeResult, string $entityName): ?string
    {
        $config = self::entityConfig()[$entityName];
        $id = $writeResult->getProperty($config['id_field']);

        // Fallback: read from payload (e.g., CMS block has pageId in payload, not as entity property)
        if ($id === null) {
            $id = $this->resolvePath($writeResult->getPayload(), $config['id_field']);
        }

        if ($id && Uuid::isValid($id)) {
            return $id;
        }
        
        return null;
    }

    private function extractActiveTimes(array $payload, string $entityName): array
    {
        $config = self::entityConfig()[$entityName];

        return [
            'activeFrom' => $this->resolvePath($payload, $config['active_from']),
            'activeUntil' => $this->resolvePath($payload, $config['active_until']),
        ];
    }

    private function resolvePath(array $data, string $path): mixed
    {
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return null;
            }
            $data = $data[$key];
        }
        return $data;
    }
}
