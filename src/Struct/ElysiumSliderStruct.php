<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Struct;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Framework\Struct\Struct;

class ElysiumSliderStruct extends Struct
{
    /**
     * @var ElysiumSlidesEntity[]|null
     */
    protected $slideCollection;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    protected $resolvedViewports;

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

    /**
     * @return array<string, array<string, mixed>>|null
     */
    public function getResolvedViewports(): ?array
    {
        return $this->resolvedViewports;
    }

    /**
     * @param array<string, array<string, mixed>> $resolvedViewports
     */
    public function setResolvedViewports(array $resolvedViewports): void
    {
        $this->resolvedViewports = $resolvedViewports;
    }
}
