<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class TimeControlSectionCacheInvalidationMessage implements AsyncMessageInterface
{
    public function __construct(
        private readonly string $sectionId,
        private readonly \DateTimeInterface $invalidationTime
    ) {}

    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    public function getInvalidationTime(): \DateTimeInterface
    {
        return $this->invalidationTime;
    }
}
