<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Blur\BlurElysiumSlider\Defaults;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationService
{
    public function validateDates(
        ?string $activeFrom,
        ?string $activeUntil,
        ConstraintViolationList $violations,
        string $propertyPath
    ): void {
        if ($activeFrom === null || $activeFrom === '' || $activeUntil === null || $activeUntil === '') {
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
}
