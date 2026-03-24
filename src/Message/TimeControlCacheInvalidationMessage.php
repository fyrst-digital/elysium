<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class TimeControlCacheInvalidationMessage implements AsyncMessageInterface
{
    public function __construct(
        private readonly string $slideId,
        private readonly \DateTimeInterface $invalidationTime
    ) {}

    public function getSlideId(): string
    {
        return $this->slideId;
    }

    public function getInvalidationTime(): \DateTimeInterface
    {
        return $this->invalidationTime;
    }
}
