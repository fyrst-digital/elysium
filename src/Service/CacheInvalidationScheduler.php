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

    public function schedule(array $payload, string $entityName, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

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

            $message = new TimeControlCacheInvalidationMessage($ids, $entityName, $datetime);

            if ($delaySeconds > 0) {
                $this->messageBus->dispatch(
                    (new Envelope($message))->with(new DelayStamp($delaySeconds * 1000))
                );
            }

        }
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
