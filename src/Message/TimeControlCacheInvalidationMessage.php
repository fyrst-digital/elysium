<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class TimeControlCacheInvalidationMessage implements AsyncMessageInterface
{
    public function __construct(
        private readonly string $entityId,
        private readonly string $entityName,
        private readonly \DateTimeInterface $invalidationTime
    ) {}

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getInvalidationTime(): \DateTimeInterface
    {
        return $this->invalidationTime;
    }
}
