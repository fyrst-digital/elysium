<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class TimeControlValidationSubscriber implements EventSubscriberInterface
{
    private const VIOLATION_CODE = 'TIME_CONTROL_INVALID_RANGE';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'validateTimeControl',
        ];
    }

    public function validateTimeControl(PreWriteValidationEvent $event): void
    {
        $violations = new ConstraintViolationList();

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
                $this->validateDates($activeFrom, $activeUntil, $violations);
                continue;
            }

            if ($id !== null && ($activeFrom === null || $activeUntil === null)) {
                $existing = $this->fetchExistingValues($id);
                $activeFrom = $activeFrom ?? ($existing['active_from'] ?? null);
                $activeUntil = $activeUntil ?? ($existing['active_until'] ?? null);
            }

            $this->validateDates($activeFrom, $activeUntil, $violations);
        }

        if (\count($violations) > 0) {
            $event->getExceptions()->add(new WriteConstraintViolationException($violations));
        }
    }

    private function validateDates(
        ?string $activeFrom,
        ?string $activeUntil,
        ConstraintViolationList $violations
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
                    'The "activeFrom" date must be before the "activeUntil" date.',
                    'The "{{ field1 }}" date must be before the "{{ field2 }}" date.',
                    [
                        '{{ field1 }}' => 'activeFrom',
                        '{{ field2 }}' => 'activeUntil',
                    ],
                    null,
                    '/activeFrom',
                    $activeFrom,
                    null,
                    self::VIOLATION_CODE
                )
            );
        }
    }

    private function fetchExistingValues(string $id): array
    {
        $result = $this->connection->fetchAssociative(
            'SELECT active_from, active_until FROM blur_elysium_slides WHERE id = :id',
            ['id' => $id]
        );

        return $result ?: [];
    }
}
