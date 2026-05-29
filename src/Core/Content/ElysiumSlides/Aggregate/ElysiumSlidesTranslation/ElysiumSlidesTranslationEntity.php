<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;

class ElysiumSlidesTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait; // adds $customFields property for CustomFields field
    /**
     * @var ElysiumSlidesEntity
     */
    protected $elysiumSlides;

    /**
     * @var string
     */
    protected $elysiumSlidesId;

    protected ?string $name;

    /**
     * @var array<mixed>|null
     */
    protected ?array $contentSettings = null;

    /**
     * Get the value of contentSettings
     *
     * @return array<mixed>|null
     */
    public function getContentSettings(): ?array
    {
        return $this->contentSettings;
    }

    /**
     * Set the value of contentSettings
     *
     * @param array<mixed>|null $contentSettings
     */
    public function setContentSettings(?array $contentSettings): void
    {
        $this->contentSettings = $contentSettings;
    }

    public function getElysiumSlides(): ElysiumSlidesEntity
    {
        return $this->elysiumSlides;
    }

    public function setElysiumSlides(ElysiumSlidesEntity $elysiumSlides): void
    {
        $this->elysiumSlides = $elysiumSlides;
    }

    public function getElysiumSlidesId(): string
    {
        return $this->elysiumSlidesId;
    }

    public function setElysiumSlidesId(string $elysiumSlidesId): void
    {
        $this->elysiumSlidesId = $elysiumSlidesId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
