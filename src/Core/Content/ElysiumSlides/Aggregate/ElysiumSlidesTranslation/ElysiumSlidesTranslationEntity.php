<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

class ElysiumSlidesTranslationEntity extends TranslationEntity
{

    /**
     * @var ElysiumSlidesEntity
     */
    protected $elysiumSlides;

    /**
     * @var string
     */
    protected $elysiumSlidesId;

    /**
     * @var string|null
     */
    protected ?string $name;

    /**
     * @var string|null
     */
    protected ?string $title;

    /**
     * @var string|null
     */
    protected ?string $description;

    /**
     * @var string|null
     */
    protected ?string $buttonLabel;

    /**
     * @var string|null
     */
    protected ?string $url;


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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getButtonLabel(): ?string
    {
        return $this->buttonLabel;
    }

    /**
     * @return void
     */
    public function setButtonLabel(string $buttonLabel): void
    {
        $this->buttonLabel = $buttonLabel;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}