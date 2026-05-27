<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Struct;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Framework\Struct\Struct;

class ElysiumSliderStruct extends Struct
{
    /**
     * @var ElysiumSlidesEntity[]|null
     */
    protected $slideCollection;

    protected ?MediaCollection $resolvedMedia = null;

    /**
     * @return ElysiumSlidesEntity[]|null
     */
    public function getSlideCollection(): ?array
    {
        return $this->slideCollection;
    }

    /**
     * @param ElysiumSlidesEntity[] $slideCollection
     */
    public function setSlideCollection(array $slideCollection): void
    {
        $this->slideCollection = $slideCollection;
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
