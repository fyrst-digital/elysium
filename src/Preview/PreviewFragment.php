<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Preview;

/**
 * Value object representing a single preview fragment.
 *
 * A fragment defines how a subset of fields is rendered in the preview.
 */
class PreviewFragment
{
    /**
     * @param string[] $watchedFields
     */
    public function __construct(
        private readonly string $name,
        private readonly string $mode,
        private readonly array $watchedFields,
        private readonly ?string $template = null,
        private readonly ?string $domSelector = null,
        private readonly ?string $insertStrategy = null,
        private readonly ?string $fallbackContainer = null,
        private readonly ?string $fallbackPosition = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @return string[]
     */
    public function getWatchedFields(): array
    {
        return $this->watchedFields;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getDomSelector(): ?string
    {
        return $this->domSelector;
    }

    public function getInsertStrategy(): ?string
    {
        return $this->insertStrategy;
    }

    public function getFallbackContainer(): ?string
    {
        return $this->fallbackContainer;
    }

    public function getFallbackPosition(): ?string
    {
        return $this->fallbackPosition;
    }

    /**
     * Check if this fragment should be triggered when the given fields change.
     *
     * @param string[] $changedFields
     */
    public function isTriggeredBy(array $changedFields): bool
    {
        return array_intersect($this->watchedFields, $changedFields) !== [];
    }
}
