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

        $result = \DateTimeImmutable::createFromFormat(
            Defaults::STORAGE_DATE_TIME_FORMAT,
            $dateTime,
            new \DateTimeZone('UTC')
        );

        if ($result === false) {
            $result = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $dateTime,
                new \DateTimeZone('UTC')
            );
        }

        return $result !== false ? $result : null;
    }
}
