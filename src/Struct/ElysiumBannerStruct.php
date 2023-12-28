<?php

namespace Blur\BlurElysiumSlider\Struct;

use Shopware\Core\Framework\Struct\Struct;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

class ElysiumBannerStruct extends Struct
{
    /**
     * @var ElysiumSlidesEntity|null
     */
    protected $elysiumSlide;

    /**
     * @return ElysiumSlidesEntity|null
     */
    public function getElysiumSlide(): ?ElysiumSlidesEntity
    {
        return $this->elysiumSlide;
    }

    /**
     * @param ElysiumSlidesEntity|null $elysiumSlide
     * @return void
     */
    public function setElysiumSlide(?ElysiumSlidesEntity $elysiumSlide): void
    {
        $this->elysiumSlide = $elysiumSlide;
    }
}