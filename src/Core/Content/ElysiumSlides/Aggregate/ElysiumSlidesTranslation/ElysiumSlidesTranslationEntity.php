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
    protected $name;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $buttonLabel;

    /**
     * @var string|null
     */
    protected $url;


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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    public function setButtonLabel(string $buttonLabel): void
    {
        $this->buttonLabel = $buttonLabel;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}