<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Struct;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Framework\Struct\Struct;

class ElysiumBannerStruct extends Struct
{
    /**
     * @var ElysiumSlidesEntity|null
     */
    protected $elysiumSlide;

    protected ?MediaCollection $resolvedMedia = null;

    public function getElysiumSlide(): ?ElysiumSlidesEntity
    {
        return $this->elysiumSlide;
    }

    public function setElysiumSlide(?ElysiumSlidesEntity $elysiumSlide): void
    {
        $this->elysiumSlide = $elysiumSlide;
    }

    public function getResolvedMedia(): ?MediaCollection
    {
        return $this->resolvedMedia;
    }

    public function setResolvedMedia(MediaCollection $resolvedMedia): void
    {
        $this->resolvedMedia = $resolvedMedia;
    }
}
