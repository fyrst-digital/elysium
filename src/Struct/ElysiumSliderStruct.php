<?php

namespace Blur\BlurElysiumSlider\Struct;

use Shopware\Core\Framework\Struct\Struct;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

class ElysiumSliderStruct extends Struct
{
    /**
     * @var ElysiumSlidesEntity[]|null
     */
    protected $slideCollection;

    /**
     * @return ElysiumSlidesEntity[]|null
     */
    public function getSlideCollection(): ?array
    {
        return $this->slideCollection;
    }

    /**
     * @param ElysiumSlidesEntity[] $slideCollection
     * @return void
     */
    public function setSlideCollection( array $slideCollection ): void
    {
        $this->slideCollection = $slideCollection;
    }
}