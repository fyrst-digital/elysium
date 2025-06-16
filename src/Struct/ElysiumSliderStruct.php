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
}
