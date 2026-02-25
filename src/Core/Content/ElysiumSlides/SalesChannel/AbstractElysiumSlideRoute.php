<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractElysiumSlideRoute
{
    abstract public function getDecorated(): AbstractElysiumSlideRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context, ?string $identifier = null): ElysiumSlideRouteResponse;

    abstract public function loadDetail(string $slideId, SalesChannelContext $context): ElysiumSlideRouteResponse;
}
