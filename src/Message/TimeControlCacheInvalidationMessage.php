<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class TimeControlCacheInvalidationMessage implements AsyncMessageInterface
{
    public function __construct(
        private readonly array $entityIds,
        private readonly string $entityName,
        private readonly \DateTimeInterface $invalidationTime
    ) {}

    public function getEntityIds(): array
    {
        return $this->entityIds;
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
