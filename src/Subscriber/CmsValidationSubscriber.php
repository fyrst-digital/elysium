<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Service\ValidationService;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\JsonUpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class CmsValidationSubscriber implements EventSubscriberInterface
{
    private const SECTION_PATH_ACTIVE_FROM = '/customFields/elysiumSectionSettings/activeFrom';

    private const BLOCK_PATH_ACTIVE_FROM = '/customFields/elysiumBlockAdvanced/activeFrom';

    private const CONFIG = [
        CmsSectionDefinition::ENTITY_NAME => [
            'settings_key' => Defaults::CMS_SECTION_SETTINGS_KEY,
            'path_from' => self::SECTION_PATH_ACTIVE_FROM,
        ],
        CmsBlockDefinition::ENTITY_NAME => [
            'settings_key' => Defaults::CMS_BLOCK_ADVANCED_KEY,
            'path_from' => self::BLOCK_PATH_ACTIVE_FROM,
        ],
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly ValidationService $validator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => [
                ['validateTimeControl', 500],
            ],
        ];
    }

    public function validateTimeControl(PreWriteValidationEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        foreach (self::CONFIG as $entityName => $config) {
            $settingsKey = $config['settings_key'];

            foreach ($event->getCommands() as $command) {
                if ($command->getEntityName() !== $entityName) {
                    continue;
                }

                [$activeFrom, $activeUntil] = match ($command->getPrivilege()) {
                    'create' => $this->extractFromInsertPayload($command->getPayload(), $settingsKey),
                    'update' => $command instanceof JsonUpdateCommand
                        ? $this->extractFromJsonUpdatePayload(
                            $command->getPayload(),
                            $settingsKey,
                            $command->getPrimaryKey(),
                            $entityName,
                        )
                        : [null, null],
                    default => [null, null],
                };

                $violations = new ConstraintViolationList();
                $this->validator->validateDates($activeFrom, $activeUntil, $violations, $config['path_from']);

                if (\count($violations) > 0) {
                    $event->getExceptions()->add(new WriteConstraintViolationException($violations, $command->getPath()));
                }
            }
        }
    }

    /**
     * @return array{?string, ?string}
     */
    private function extractFromInsertPayload(array $payload, string $settingsKey): array
    {
        $raw = $payload['custom_fields'] ?? null;

        if (!\is_string($raw) || $raw === '') {
            return [null, null];
        }

        $customFields = \json_decode($raw, true);
        if (!\is_array($customFields)) {
            return [null, null];
        }

        $settings = $customFields[$settingsKey] ?? [];
        if (!\is_array($settings)) {
            return [null, null];
        }

        return [
            $this->extractString($settings, 'activeFrom'),
            $this->extractString($settings, 'activeUntil'),
        ];
    }

    /**
     * @param array<string, string> $primaryKey
     * @return array{?string, ?string}
     */
    private function extractFromJsonUpdatePayload(
        array $payload,
        string $settingsKey,
        array $primaryKey,
        string $entityName
    ): array {
        $settings = $payload[$settingsKey] ?? null;

        if (!\is_array($settings)) {
            return [null, null];
        }

        $activeFrom = $this->extractString($settings, 'activeFrom');
        $activeUntil = $this->extractString($settings, 'activeUntil');

        // Merge with existing values when only one field is provided
        if ($activeFrom === null || $activeUntil === null) {
            $id = $primaryKey['id'] ?? null;

            if ($id !== null) {
                $existing = $this->fetchExistingCustomFields($id, $entityName, $settingsKey);

                if ($activeFrom === null) {
                    $activeFrom = $existing['activeFrom'] ?? null;
                }
                if ($activeUntil === null) {
                    $activeUntil = $existing['activeUntil'] ?? null;
                }
            }
        }

        return [$activeFrom, $activeUntil];
    }

    /**
     * @return array<string, ?string>
     */
    private function fetchExistingCustomFields(string $id, string $entityName, string $settingsKey): array
    {
        $result = $this->connection->fetchAssociative(
            \sprintf('SELECT custom_fields FROM %s WHERE id = :id', $entityName),
            ['id' => $id],
            ['id' => ArrayParameterType::BINARY]
        );

        if (!$result) {
            return [];
        }

        $customFields = $result['custom_fields'] ?? null;
        if (\is_string($customFields)) {
            $customFields = \json_decode($customFields, true) ?? [];
        }

        if (!\is_array($customFields)) {
            return [];
        }

        $settings = $customFields[$settingsKey] ?? [];
        if (!\is_array($settings)) {
            return [];
        }

        return [
            'activeFrom' => $this->extractString($settings, 'activeFrom'),
            'activeUntil' => $this->extractString($settings, 'activeUntil'),
        ];
    }

    private function extractString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return \is_string($value) && $value !== '' ? $value : null;
    }
}
