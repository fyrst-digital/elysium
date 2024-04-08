<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Media\MediaEntity;

class ElysiumSlidesEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $productId;

    /**
     * @var ?ProductEntity
     */
    protected ?ProductEntity $product;

    /**
     * @var string|null
     */
    protected ?string $slideCoverId;

    /** 
     * @var ?MediaEntity
     */
    protected ?MediaEntity $slideCover;

    /**
     * @var string|null
     */
    protected ?string $slideCoverMobileId;

    /**
     * @var ?MediaEntity
     */
    protected ?MediaEntity $slideCoverMobile;

    /**
     * @var string|null
     */
    protected ?string $slideCoverTabletId;

    /**
     * @var ?MediaEntity
     */
    protected ?MediaEntity $slideCoverTablet;

    /**
     * @var ?string
     */
    protected ?string $slideCoverVideoId;

    /**
     * @var ?MediaEntity
     */
    protected ?MediaEntity $slideCoverVideo;

    /**
     * @var ?string
     */
    protected ?string $presentationMediaId;

    /**
     * @var ?MediaEntity
     */
    protected ?MediaEntity $presentationMedia;

    /**
     * @var mixed[]|null
     */
    protected ?array $slideSettings;

    /**
     * @var ?string
     */
    protected ?string $name;

    /**
     * @var ?string
     */
    protected ?string $title;

    /**
     * @var ?string
     */
    protected ?string $description;

    /**
     * @var ?string
     */
    protected ?string $button_label;

    /**
     * @var ?string
     */
    protected ?string $url;

    /**
     * Get the value of productId
     * @return string|null
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * Set the value of productId
     * @return void
     */
    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * Get the value of product
     * @return ProductEntity|null
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * Set the value of product
     * @return void
     */
    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    /**
     * Get the value of slideCoverId
     * @return string|null
     */
    public function getSlideCoverId(): ?string
    {
        return $this->slideCoverId;
    }

    /**
     * Set the value of slideCoverId
     * @return void
     */
    public function setSlideCoverId(?string $slideCoverId): void
    {
        $this->slideCoverId = $slideCoverId;
    }

    /**
     * Get the value of slideCover
     * @return ?MediaEntity
     */
    public function getSlideCover(): ?MediaEntity
    {
        return $this->slideCover;
    }

    /**
     * Set the value of slideCover
     * @param  ?MediaEntity  $slideCover
     * @return  void
     */
    public function setSlideCover(?MediaEntity $slideCover): void
    {
        $this->slideCover = $slideCover;
    }

    /**
     * Get the value of slideCoverMobileId
     * @return  string|null
     */
    public function getSlideCoverMobileId(): ?string
    {
        return $this->slideCoverMobileId;
    }

    /**
     * Set the value of slideCoverMobileId
     * @param  string|null  $slideCoverMobileId
     * @return  self
     */
    public function setSlideCoverMobileId($slideCoverMobileId): void
    {
        $this->slideCoverMobileId = $slideCoverMobileId;
    }

    /**
     * Get the value of slideCoverMobile
     *
     * @return  ?MediaEntity
     */
    public function getSlideCoverMobile(): ?MediaEntity
    {
        return $this->slideCoverMobile;
    }

    /**
     * Set the value of slideCoverMobile
     * @param   ?MediaEntity  $slideCoverMobile  
     * @return  void
     */
    public function setSlideCoverMobile(?MediaEntity $slideCoverMobile): void
    {
        $this->slideCoverMobile = $slideCoverMobile;
    }

    /**
     * Get the value of slideCoverTabletId
     * @return  string|null
     */
    public function getSlideCoverTabletId(): ?string
    {
        return $this->slideCoverTabletId;
    }

    /**
     * Set the value of slideCoverTabletId
     * @param   string|null  $slideCoverTabletId  
     * @return  void
     */
    public function setSlideCoverTabletId($slideCoverTabletId): void
    {
        $this->slideCoverTabletId = $slideCoverTabletId;
    }

    /**
     * Get the value of slideCoverTablet
     * @return  ?MediaEntity
     */
    public function getSlideCoverTablet(): ?MediaEntity
    {
        return $this->slideCoverTablet;
    }

    /**
     * Set the value of slideCoverTablet
     * @param   ?MediaEntity  $slideCoverTablet  
     * @return  void
     */
    public function setSlideCoverTablet(?MediaEntity $slideCoverTablet): void
    {
        $this->slideCoverTablet = $slideCoverTablet;
    }

    /**
     * Get the value of slideCoverVideoId
     * @return  ?string
     */
    public function getSlideCoverVideoId(): ?string
    {
        return $this->slideCoverVideoId;
    }

    /**
     * Set the value of slideCoverVideoId
     * @param   ?string  $slideCoverVideoId  
     * @return  void
     */
    public function setSlideCoverVideoId(?string $slideCoverVideoId): void
    {
        $this->slideCoverVideoId = $slideCoverVideoId;
    }

    /**
     * Get the value of slideCoverVideo
     * @return  ?MediaEntity
     */
    public function getSlideCoverVideo(): ?MediaEntity
    {
        return $this->slideCoverVideo;
    }

    /**
     * Set the value of slideCoverVideo
     * @param   ?MediaEntity  $slideCoverVideo  
     * @return  void
     */
    public function setSlideCoverVideo(?MediaEntity $slideCoverVideo): void
    {
        $this->slideCoverVideo = $slideCoverVideo;
    }

    /**
     * Get the value of presentationMediaId
     * @return  ?string
     */
    public function getPresentationMediaId(): ?string
    {
        return $this->presentationMediaId;
    }

    /**
     * Set the value of presentationMediaId
     * @param   ?string  $presentationMediaId  
     * @return  void
     */
    public function setPresentationMediaId(?string $presentationMediaId): void
    {
        $this->presentationMediaId = $presentationMediaId;
    }

    /**
     * Get the value of presentationMedia
     * @return  ?MediaEntity
     */
    public function getPresentationMedia(): ?MediaEntity
    {
        return $this->presentationMedia;
    }

    /**
     * Set the value of presentationMedia
     * @param   ?MediaEntity  $presentationMedia  
     * @return  void
     */
    public function setPresentationMedia(?MediaEntity $presentationMedia): void
    {
        $this->presentationMedia = $presentationMedia;
    }

    /**
     * Get the value of slideSettings
     * @return  mixed[]|null
     */
    public function getSlideSettings(): ?array
    {
        return $this->slideSettings;
    }

    /**
     * Set the value of slideSettings
     * @param   mixed[]|null  $slideSettings  
     * @return  void
     */
    public function setSlideSettings(?array $slideSettings): void
    {
        $this->slideSettings = $slideSettings;
    }

    /**
     * Get the value of name
     * @return  ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     * @param   ?string  $name  
     * @return  void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the value of title
     * @return  ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     * @param   ?string  $title  
     * @return  void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the value of description
     * @return  ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     * @param   ?string  $description  
     * @return  void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of button_label
     * @return  ?string
     */
    public function getButtonLabel(): ?string
    {
        return $this->button_label;
    }

    /**
     * Set the value of button_label
     * @param   ?string  $button_label  
     * @return  void
     */
    public function setButtonLabel(?string $button_label): void
    {
        $this->button_label = $button_label;
    }

    /**
     * Get the value of url
     * @return  ?string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     * @param   ?string  $url  
     * @return  void
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}
