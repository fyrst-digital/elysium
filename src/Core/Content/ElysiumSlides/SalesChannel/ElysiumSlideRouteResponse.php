<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class ElysiumSlideRouteResponse extends StoreApiResponse
{
    public function __construct(
        private readonly ElysiumSlidesCollection $slides,
    ) {
        parent::__construct($slides);
    }

    public function getSlides(): ElysiumSlidesCollection
    {
        return $this->slides;
    }
}
