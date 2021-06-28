<?php

namespace Blur\ElysiumBlocks\Struct;

use Shopware\Core\Framework\Struct\Struct;

class ElysiumSliderStruct extends Struct
{
    /**
     * @var array|null
     */
    protected $slideCollection;

    public function getSlideCollection(): ?array
    {
        return $this->slideCollection;
    }

    public function setSlideCollection( array $slideCollection ): void
    {
        $this->slideCollection = $slideCollection;
    }
}