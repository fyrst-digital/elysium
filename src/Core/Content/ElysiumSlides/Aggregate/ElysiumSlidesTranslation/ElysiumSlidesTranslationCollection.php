<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ElysiumSlidesTranslationEntity>
 */
class ElysiumSlidesTranslationCollection extends EntityCollection
{
    /**
     * @return array<string>
     */
    public function getElysiumSlidesIds(): array
    {
        return $this->fmap(fn(ElysiumSlidesTranslationEntity $elysiumSlidesTranslation) => $elysiumSlidesTranslation->getElysiumSlidesId());
    }

    public function filterByElysiumSlidesId(string $id): self
    {
        return $this->filter(fn(ElysiumSlidesTranslationEntity $elysiumSlidesTranslation) => $elysiumSlidesTranslation->getElysiumSlidesId() === $id);
    }

    /**
     * @return array<string>
     */
    public function getLanguageIds(): array
    {
        return $this->fmap(fn(ElysiumSlidesTranslationEntity $elysiumSlidesTranslation) => $elysiumSlidesTranslation->getLanguageId());
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(fn(ElysiumSlidesTranslationEntity $elysiumSlidesTranslation) => $elysiumSlidesTranslation->getLanguageId() === $id);
    }

    public function getApiAlias(): string
    {
        return 'elysium_slides_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return ElysiumSlidesTranslationEntity::class;
    }
}
