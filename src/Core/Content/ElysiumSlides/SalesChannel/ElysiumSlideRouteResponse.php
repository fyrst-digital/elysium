<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class ElysiumSlideRouteResponse extends StoreApiResponse
{
    public function getSlides(): ElysiumSlidesCollection
    {
        return $this->object;
    }
}
