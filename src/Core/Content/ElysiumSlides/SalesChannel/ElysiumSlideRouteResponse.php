<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\System\SalesChannel\StoreApiResponse;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ElysiumSlideRouteResponse extends StoreApiResponse
{
    public function __construct(
        private readonly ElysiumSlidesCollection|EntityCollection $slides,
    ) {
        parent::__construct($slides);
    }

    public function getSlides(): ElysiumSlidesCollection|EntityCollection
    {
        return $this->slides;
    }
}
