<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Preview;

use Blur\BlurElysiumSlider\Preview\PreviewFragment;

class PreviewSchema
{
    /**
     * @param array<int, array{path: string, fields: string[], deep?: bool}> $fieldMappings
     * @param array<int, PreviewFragment> $fragments
     */
    public function __construct(
        private readonly string $elementType,
        private readonly array $fieldMappings,
        private readonly array $fragments,
    ) {}

    public function getElementType(): string
    {
        return $this->elementType;
    }

    /**
     * @return array<int, array{path: string, fields: string[], deep?: bool}>
     */
    public function getFieldMappings(): array
    {
        return $this->fieldMappings;
    }

    /**
     * @return array<int, PreviewFragment>
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    /**
     * @param string[] $changedFields
     * @return PreviewFragment[]
     */
    public function getFragmentsForFields(array $changedFields): array
    {
        $triggered = [];
        $hasSlideWildcard = in_array('slide', $changedFields, true);

        foreach ($this->fragments as $fragment) {
            if ($hasSlideWildcard || $fragment->isTriggeredBy($changedFields)) {
                $triggered[] = $fragment;
            }
        }

        return $triggered;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'elementType' => $this->elementType,
            'fieldMappings' => $this->fieldMappings,
            'fragments' => array_map(static fn (PreviewFragment $f) => $f->toArray(), $this->fragments),
        ];
    }
}
