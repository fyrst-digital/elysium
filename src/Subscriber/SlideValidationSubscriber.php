<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\JsonUpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;


class SlideValidationSubscriber implements EventSubscriberInterface
{    private const NAME_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9\s-]*[A-Za-z0-9]$|^[A-Za-z0-9]$/';

    private const ELYSIUM_BLOCK_TYPES = [
        'blur-elysium-banner',
        'blur-elysium-slider',
    ];

    public function __construct(
        private readonly Connection $connection
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => [
                ['validateSlideName', 500],
                ['validateTimeControl', 500],
                //['validateCmsSectionTimeControl', 400],
                //['validateCmsBlock', 600],
            ],
        ];
    }


    public function validateSlideName(PreWriteValidationEvent $event): void
    {
        $violations = new ConstraintViolationList();

        foreach ($event->getCommands() as $command) {
            if ($command->getEntityName() !== ElysiumSlidesTranslationDefinition::ENTITY_NAME) {
                continue;
            }

            $privilege = $command->getPrivilege();
            if ($privilege === 'delete') {
                continue;
            }

            $payload = $command->getPayload();
            $name = $payload['name'] ?? null;

            if ($name === null) {
                continue;
            }

            if (!preg_match(self::NAME_PATTERN, $name)) {
                $violations->add(
                    new ConstraintViolation(
                        "Slide name contains invalid characters. Only letters, numbers, spaces, and dashes are allowed. The name must not start or end with whitespace.",
                        'Slide {{ field }} contains invalid characters. Only letters, numbers, spaces, and dashes are allowed. The name must not start or end with whitespace.',
                        [
                            '{{ field }}' => 'name',
                        ],
                        null,
                        '/name',
                        $name,
                        null,
                        Defaults::ERROR_CODE_NAME_FORMAT
                    )
                );
            }

            if (\count($violations) > 0) {
                $event->getExceptions()->add(new WriteConstraintViolationException($violations, $command->getPath()));
            }
        }
    }

    public function validateTimeControl(PreWriteValidationEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $violations = new ConstraintViolationList();

        $primaryKeys = $event->getPrimaryKeys(ElysiumSlidesDefinition::ENTITY_NAME);
        $ids = array_column($primaryKeys, 'id');
        $existingSlidesData = empty($ids) ? [] : $this->fetchExistingValuesBulk($ids);

        foreach ($event->getCommands() as $command) {
            $entityName = $command->getEntityName();
            if ($entityName !== ElysiumSlidesDefinition::ENTITY_NAME) {
                continue;
            }

            $privilege = $command->getPrivilege();
            if ($privilege === 'delete') {
                continue;
            }

            $payload = $command->getPayload();
            $primaryKey = $command->getPrimaryKey();
            $id = $primaryKey['id'] ?? null;

            $activeFrom = $payload['active_from'] ?? null;
            $activeUntil = $payload['active_until'] ?? null;

            if ($privilege === 'create') {
                $this->validateDates($activeFrom, $activeUntil, $violations, '/activeFrom');
                continue;
            }

            if ($id !== null && ($activeFrom === null || $activeUntil === null)) {
                $existing = $existingSlidesData[$id] ?? [];
                $activeFrom = array_key_exists('active_from', $payload) ? $activeFrom : ($existing['active_from'] ?? null);
                $activeUntil = array_key_exists('active_until', $payload) ? $activeUntil : ($existing['active_until'] ?? null);
            }

            $this->validateDates($activeFrom, $activeUntil, $violations, '/activeFrom');

            if (\count($violations) > 0) {
                $event->getExceptions()->add(new WriteConstraintViolationException($violations, $command->getPath()));
            }
        }

    }

    private function validateDates(
        ?string $activeFrom,
        ?string $activeUntil,
        ConstraintViolationList $violations,
        string $propertyPath
    ): void {
        if ($activeFrom === null || $activeUntil === null) {
            return;
        }

        $fromTimestamp = strtotime($activeFrom);
        $untilTimestamp = strtotime($activeUntil);

        if ($fromTimestamp === false || $untilTimestamp === false) {
            return;
        }

        if ($fromTimestamp >= $untilTimestamp) {
            $violations->add(
                new ConstraintViolation(
                    "The \"activeFrom\" date must be before the \"activeUntil\" date.",
                    'The "{{ field1 }}" date must be before the "{{ field2 }}" date.',
                    [
                        '{{ field1 }}' => 'activeFrom',
                        '{{ field2 }}' => 'activeUntil',
                    ],
                    null,
                    $propertyPath,
                    $activeFrom,
                    null,
                    Defaults::ERROR_CODE_TIME_CONTROL
                )
            );
        }
    }

    private function fetchExistingValuesBulk(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $results = $this->connection->fetchAllAssociative(
            'SELECT id, active_from, active_until FROM blur_elysium_slides WHERE id IN (:ids)',
            ['ids' => $ids],
            ['ids' => ArrayParameterType::BINARY]
        );

        $data = [];
        foreach ($results as $row) {
            $data[$row['id']] = [
                'active_from' => $row['active_from'] ?? null,
                'active_until' => $row['active_until'] ?? null,
            ];
        }

        return $data;
    }

    public function validateCmsSectionTimeControl(PreWriteValidationEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $violations = new ConstraintViolationList();

        $primaryKeys = $event->getPrimaryKeys('cms_section');
        $ids = array_column($primaryKeys, 'id');
        $existingData = empty($ids) ? [] : $this->fetchExistingCustomFieldsBulk('cms_section', $ids);

        foreach ($event->getCommands() as $command) {
            if ($command->getEntityName() !== 'cms_section') {
                continue;
            }

            $privilege = $command->getPrivilege();
            if ($privilege === 'delete') {
                continue;
            }

            $payload = $command->getPayload();
            $settingsKey = Defaults::CMS_SECTION_SETTINGS_KEY;

            if (($payload['type'] ?? null) !== Defaults::CMS_SECTION_NAME && !\array_key_exists($settingsKey, $payload)) {
                continue;
            }

            $payloadSettings = $this->extractSettingsPayload($payload, $settingsKey);

            if ($payloadSettings === null) {
                continue;
            }

            $activeFrom = $payloadSettings['activeFrom'] ?? null;
            $activeUntil = $payloadSettings['activeUntil'] ?? null;

            $basePath = \array_key_exists('custom_fields', $payload)
                ? '/customFields/' . $settingsKey
                : '/' . $settingsKey;

            if ($privilege === 'create') {
                $this->validateDates($activeFrom, $activeUntil, $violations, $basePath . '/activeFrom');
                continue;
            }

            $primaryKey = $command->getPrimaryKey();
            $id = $primaryKey['id'] ?? null;

            if ($id !== null) {
                $existing = $existingData[$id] ?? [];
                $existingSettings = $existing[$settingsKey] ?? [];

                if (!\array_key_exists('activeFrom', $payloadSettings)) {
                    $activeFrom = $existingSettings['activeFrom'] ?? null;
                }
                if (!\array_key_exists('activeUntil', $payloadSettings)) {
                    $activeUntil = $existingSettings['activeUntil'] ?? null;
                }
            }

            $this->validateDates($activeFrom, $activeUntil, $violations, $basePath . '/activeFrom');
        }

        if (\count($violations) > 0) {
            $event->getExceptions()->add(new WriteConstraintViolationException($violations));
        }
    }

    public function validateCmsBlock(PreWriteValidationEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $violations = new ConstraintViolationList();

        $primaryKeys = $event->getPrimaryKeys(CmsBlockDefinition::ENTITY_NAME);
        $ids = array_column($primaryKeys, 'id');
        $existingData = empty($ids) ? [] : $this->fetchExistingCustomFieldsBulk(CmsBlockDefinition::ENTITY_NAME, $ids);

        foreach ($event->getCommands() as $command) {
            if ($command->getEntityName() !== CmsBlockDefinition::ENTITY_NAME) {
                continue;
            }

            if (!$command instanceof JsonUpdateCommand) {
                continue;
            }

            $privilege = $command->getPrivilege();
            if ($privilege === 'delete') {
                continue;
            }

            $payload = $command->getPayload();

            if (($payload['type'] ?? null) !== null && !\in_array($payload['type'], self::ELYSIUM_BLOCK_TYPES, true)) {
                continue;
            }

            $settingsKey = Defaults::CMS_BLOCK_ADVANCED_KEY;
            $payloadSettings = $this->extractSettingsPayload($payload, $settingsKey);

            if ($payloadSettings === null) {
                continue;
            }

            $activeFrom = $payloadSettings['activeFrom'] ?? null;
            $activeUntil = $payloadSettings['activeUntil'] ?? null;

            #$basePath = "/cms_block/{$command->getDecodedPrimaryKey()["id"]}/customFields/{$settingsKey}";
            $basePath = "/customFields/{$settingsKey}";
            
            if ($privilege === 'create') {
                $this->validateDates($activeFrom, $activeUntil, $violations, $basePath . '/activeFrom');
                continue;
            }

            $primaryKey = $command->getPrimaryKey();
            $id = $primaryKey['id'] ?? null;

            if ($id !== null) {
                $existing = $existingData[$id] ?? [];
                $existingSettings = $existing[$settingsKey] ?? [];

                if (!\array_key_exists('activeFrom', $payloadSettings)) {
                    $activeFrom = $existingSettings['activeFrom'] ?? null;
                }
                if (!\array_key_exists('activeUntil', $payloadSettings)) {
                    $activeUntil = $existingSettings['activeUntil'] ?? null;
                }
            }

            $this->validateDates($activeFrom, $activeUntil, $violations, $basePath . '/activeFrom');

            if (\count($violations) > 0) {
                $event->getExceptions()->add(new WriteConstraintViolationException($violations, $command->getPath()));
            }
        }

    }

    /**
     * Extracts Elysium settings from a WriteCommand payload.
     *
     * Handles two payload formats:
     * - INSERT: `custom_fields` key contains a JSON-encoded string
     * - UPDATE (JsonUpdateCommand): settings key is present directly in the decoded payload
     *
     * Returns null if no relevant settings are found (command should be skipped).
     *
     * @return array<string, mixed>|null
     */
    private function extractSettingsPayload(array $payload, string $settingsKey): ?array
    {
        // INSERT command: custom_fields contains a JSON-encoded string
        if (\array_key_exists('custom_fields', $payload)) {
            $customFields = \is_string($payload['custom_fields'])
                ? \json_decode($payload['custom_fields'], true)
                : $payload['custom_fields'];

            if (!\is_array($customFields) || !\array_key_exists($settingsKey, $customFields)) {
                return null;
            }

            return $customFields[$settingsKey];
        }

        // UPDATE via JsonUpdateCommand: settings key is directly in decoded payload
        if (\array_key_exists($settingsKey, $payload)) {
            return $payload[$settingsKey];
        }

        return null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function fetchExistingCustomFieldsBulk(string $tableName, array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $results = $this->connection->fetchAllAssociative(
            \sprintf('SELECT HEX(id) as id, custom_fields FROM %s WHERE id IN (:ids)', $tableName),
            ['ids' => $ids],
            ['ids' => ArrayParameterType::BINARY]
        );

        $data = [];
        foreach ($results as $row) {
            $customFields = $row['custom_fields'] !== null ? \json_decode($row['custom_fields'], true) : [];
            $data[$row['id']] = $customFields;
        }

        return $data;
    }
}
