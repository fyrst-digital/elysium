<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Service\DateTimeParser;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class TimeControlValidationSubscriber implements EventSubscriberInterface
{
    private const VIOLATION_CODE = 'TIME_CONTROL_INVALID_RANGE';

    public function __construct(
        private readonly Connection $connection,
        private readonly DateTimeParser $dateTimeParser
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'validateTimeControl',
        ];
    }

    /**
     * @todo the whole method with dependend functions looks awkward, not readable and not maintanable. It shoud follow clean code principles
     * consider refractoring of this. Simplify it drasticly
     */

    public function validateTimeControl(PreWriteValidationEvent $event): void
    {
        if (!Feature::isActive('elysium_preview_time_control')) {
            return;
        }

        $violations = new ConstraintViolationList();
        $idsNeedingFetch = [];
        $commandsToValidate = [];

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
            $id = $command->getPrimaryKey()['id'] ?? null;
            $activeFrom = $payload['active_from'] ?? null;
            $activeUntil = $payload['active_until'] ?? null;

            if ($privilege === 'create') {
                $this->validateDates($activeFrom, $activeUntil, $violations);
                continue;
            }

            if ($id !== null && ($activeFrom === null || $activeUntil === null)) {
                $idsNeedingFetch[$id] = true;
            }

            $commandsToValidate[] = ['id' => $id, 'activeFrom' => $activeFrom, 'activeUntil' => $activeUntil];
        }

        $existingValuesMap = $this->fetchExistingValuesBatch(array_keys($idsNeedingFetch));

        foreach ($commandsToValidate as $item) {
            $id = $item['id'];
            $activeFrom = $item['activeFrom'] ?? $existingValuesMap[$id]['active_from'] ?? null;
            $activeUntil = $item['activeUntil'] ?? $existingValuesMap[$id]['active_until'] ?? null;

            $this->validateDates($activeFrom, $activeUntil, $violations);
        }

        if (\count($violations) > 0) {
            $event->getExceptions()->add(new WriteConstraintViolationException($violations));
        }
    }

    private function validateDates(?string $activeFrom, ?string $activeUntil, ConstraintViolationList $violations): void
    {
        if ($activeFrom === null || $activeUntil === null) {
            return;
        }

        $fromDate = $this->dateTimeParser->parseFromStorage($activeFrom);
        $untilDate = $this->dateTimeParser->parseFromStorage($activeUntil);

        if ($fromDate === null || $untilDate === null || $fromDate < $untilDate) {
            return;
        }

        $violations->add(new ConstraintViolation(
            'The "activeFrom" date must be before the "activeUntil" date.',
            'The "{{ field1 }}" date must be before the "{{ field2 }}" date.',
            ['{{ field1 }}' => 'activeFrom', '{{ field2 }}' => 'activeUntil'],
            null,
            '/activeFrom',
            $activeFrom,
            null,
            self::VIOLATION_CODE
        ));
    }

    private function fetchExistingValuesBatch(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = array_map(fn($i) => ":id_$i", array_keys($ids));
        $params = array_combine($placeholders, $ids);

        $sql = sprintf(
            'SELECT LOWER(HEX(id)) as id, active_from, active_until FROM blur_elysium_slides WHERE id IN (%s)',
            implode(', ', $placeholders)
        );

        $results = $this->connection->fetchAllAssociative($sql, $params);

        $mapped = [];
        foreach ($results as $result) {
            $id = $result['id'];
            $mapped[$id] = ['active_from' => $result['active_from'], 'active_until' => $result['active_until']];
        }

        return $mapped;
    }
}
