<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationCollection;

class ElysiumSlidesEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;
    use ContentSettingsTrait;

    protected ?string $productId = null;

    protected ?ProductEntity $product = null;

    protected ?string $categoryId = null;

    protected ?CategoryEntity $category = null;

    protected ?\DateTimeInterface $activeFrom = null;

    protected ?\DateTimeInterface $activeUntil = null;

    protected ?ElysiumSlidesTranslationCollection $translations = null;

    /**
     * @var mixed[]|null
     */
    protected ?array $slideSettings = [];

    protected ?string $name = null;

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function getActiveFrom(): ?\DateTimeInterface
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(?\DateTimeInterface $activeFrom): void
    {
        $this->activeFrom = $activeFrom;
    }

    public function getActiveUntil(): ?\DateTimeInterface
    {
        return $this->activeUntil;
    }

    public function setActiveUntil(?\DateTimeInterface $activeUntil): void
    {
        $this->activeUntil = $activeUntil;
    }

    public function getTranslations(): ?ElysiumSlidesTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(?ElysiumSlidesTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return mixed[]|null
     */
    public function getSlideSettings(): ?array
    {
        return $this->slideSettings;
    }

    /**
     * @param mixed[]|null $slideSettings
     */
    public function setSlideSettings(?array $slideSettings): void
    {
        $this->slideSettings = $slideSettings;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
