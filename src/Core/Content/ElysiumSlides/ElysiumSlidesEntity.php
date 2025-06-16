<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ElysiumSlidesEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected ?string $productId;

    protected ?ProductEntity $product;

    protected ?string $slideCoverId;

    protected ?MediaEntity $slideCover;

    protected ?string $slideCoverMobileId;

    protected ?MediaEntity $slideCoverMobile;

    protected ?string $slideCoverTabletId;

    protected ?MediaEntity $slideCoverTablet;

    protected ?string $slideCoverVideoId;

    protected ?MediaEntity $slideCoverVideo;

    protected ?string $presentationMediaId;

    protected ?MediaEntity $presentationMedia;

    /**
     * @var mixed[]|null
     */
    protected ?array $slideSettings;

    protected ?string $name;

    protected ?string $title;

    protected ?string $description;

    protected ?string $buttonLabel;

    protected ?string $url;

    /**
     * Get the value of productId
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * Set the value of productId
     */
    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * Get the value of product
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * Set the value of product
     */
    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    /**
     * Get the value of slideCoverId
     */
    public function getSlideCoverId(): ?string
    {
        return $this->slideCoverId;
    }

    /**
     * Set the value of slideCoverId
     */
    public function setSlideCoverId(?string $slideCoverId): void
    {
        $this->slideCoverId = $slideCoverId;
    }

    /**
     * Get the value of slideCover
     */
    public function getSlideCover(): ?MediaEntity
    {
        return $this->slideCover;
    }

    /**
     * Set the value of slideCover
     */
    public function setSlideCover(?MediaEntity $slideCover): void
    {
        $this->slideCover = $slideCover;
    }

    /**
     * Get the value of slideCoverMobileId
     */
    public function getSlideCoverMobileId(): ?string
    {
        return $this->slideCoverMobileId;
    }

    /**
     * Set the value of slideCoverMobileId
     *
     * @param string|null $slideCoverMobileId
     */
    public function setSlideCoverMobileId($slideCoverMobileId): void
    {
        $this->slideCoverMobileId = $slideCoverMobileId;
    }

    /**
     * Get the value of slideCoverMobile
     */
    public function getSlideCoverMobile(): ?MediaEntity
    {
        return $this->slideCoverMobile;
    }

    /**
     * Set the value of slideCoverMobile
     */
    public function setSlideCoverMobile(?MediaEntity $slideCoverMobile): void
    {
        $this->slideCoverMobile = $slideCoverMobile;
    }

    /**
     * Get the value of slideCoverTabletId
     */
    public function getSlideCoverTabletId(): ?string
    {
        return $this->slideCoverTabletId;
    }

    /**
     * Set the value of slideCoverTabletId
     *
     * @param string|null $slideCoverTabletId
     */
    public function setSlideCoverTabletId($slideCoverTabletId): void
    {
        $this->slideCoverTabletId = $slideCoverTabletId;
    }

    /**
     * Get the value of slideCoverTablet
     */
    public function getSlideCoverTablet(): ?MediaEntity
    {
        return $this->slideCoverTablet;
    }

    /**
     * Set the value of slideCoverTablet
     */
    public function setSlideCoverTablet(?MediaEntity $slideCoverTablet): void
    {
        $this->slideCoverTablet = $slideCoverTablet;
    }

    /**
     * Get the value of slideCoverVideoId
     */
    public function getSlideCoverVideoId(): ?string
    {
        return $this->slideCoverVideoId;
    }

    /**
     * Set the value of slideCoverVideoId
     */
    public function setSlideCoverVideoId(?string $slideCoverVideoId): void
    {
        $this->slideCoverVideoId = $slideCoverVideoId;
    }

    /**
     * Get the value of slideCoverVideo
     */
    public function getSlideCoverVideo(): ?MediaEntity
    {
        return $this->slideCoverVideo;
    }

    /**
     * Set the value of slideCoverVideo
     */
    public function setSlideCoverVideo(?MediaEntity $slideCoverVideo): void
    {
        $this->slideCoverVideo = $slideCoverVideo;
    }

    /**
     * Get the value of presentationMediaId
     */
    public function getPresentationMediaId(): ?string
    {
        return $this->presentationMediaId;
    }

    /**
     * Set the value of presentationMediaId
     */
    public function setPresentationMediaId(?string $presentationMediaId): void
    {
        $this->presentationMediaId = $presentationMediaId;
    }

    /**
     * Get the value of presentationMedia
     */
    public function getPresentationMedia(): ?MediaEntity
    {
        return $this->presentationMedia;
    }

    /**
     * Set the value of presentationMedia
     */
    public function setPresentationMedia(?MediaEntity $presentationMedia): void
    {
        $this->presentationMedia = $presentationMedia;
    }

    /**
     * Get the value of slideSettings
     *
     * @return mixed[]|null
     */
    public function getSlideSettings(): ?array
    {
        return $this->slideSettings;
    }

    /**
     * Set the value of slideSettings
     *
     * @param mixed[]|null $slideSettings
     */
    public function setSlideSettings(?array $slideSettings): void
    {
        $this->slideSettings = $slideSettings;
    }

    /**
     * Get the value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the value of title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of buttonLabel
     */
    public function getButtonLabel(): ?string
    {
        return $this->buttonLabel;
    }

    /**
     * Set the value of buttonLabel
     */
    public function setButtonLabel(?string $buttonLabel): void
    {
        $this->buttonLabel = $buttonLabel;
    }

    /**
     * Get the value of url
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}
