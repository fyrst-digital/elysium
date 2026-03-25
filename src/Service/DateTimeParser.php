<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Shopware\Core\Defaults;

class DateTimeParser
{
    public function parseFromStorage(?string $dateTime): ?\DateTimeImmutable
    {
        if ($dateTime === null) {
            return null;
        }

        return \DateTimeImmutable::createFromFormat(
            Defaults::STORAGE_DATE_TIME_FORMAT,
            $dateTime,
            new \DateTimeZone('UTC')
        ) ?: \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $dateTime,
            new \DateTimeZone('UTC')
        );
    }
}
