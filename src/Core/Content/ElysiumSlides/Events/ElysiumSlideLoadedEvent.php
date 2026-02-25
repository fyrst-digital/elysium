<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ElysiumSlideLoadedEvent extends NestedEvent implements ShopwareSalesChannelEvent
{
    protected ElysiumSlidesCollection $slides;

    protected SalesChannelContext $salesChannelContext;

    public function __construct(
        ElysiumSlidesCollection $slides,
        SalesChannelContext $salesChannelContext
    ) {
        $this->slides = $slides;
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getSlides(): ElysiumSlidesCollection
    {
        return $this->slides;
    }

    public function setSlides(ElysiumSlidesCollection $slides): void
    {
        $this->slides = $slides;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
