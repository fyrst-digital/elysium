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

    protected ?string $title;

    protected ?string $description;

    protected ?string $buttonLabel;

    protected ?string $url;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getButtonLabel(): ?string
    {
        return $this->buttonLabel;
    }

    public function setButtonLabel(string $buttonLabel): void
    {
        $this->buttonLabel = $buttonLabel;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
