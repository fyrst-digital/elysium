<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

class DateTimeParser
{
    private const STORAGE_FORMAT = 'Y-m-d H:i:s';

    public function parseFromStorage(?string $dateTime): ?\DateTimeImmutable
    {
        if ($dateTime === null) {
            return null;
        }

        $result = \DateTimeImmutable::createFromFormat(
            self::STORAGE_FORMAT,
            $dateTime,
            new \DateTimeZone('UTC')
        );

        return $result !== false ? $result : null;
    }
}
