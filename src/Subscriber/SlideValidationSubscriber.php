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
{    
    private const NAME_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9\s-]*[A-Za-z0-9]$|^[A-Za-z0-9]$/';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => [
                ['validateSlideName', 500],
                ['validateTimeControl', 500],
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
}
