<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface ElysiumSlideLoaderInterface
{
    public function load(array $slideIds, ?Criteria $criteria, SalesChannelContext $context, ?string $identifier = null): ElysiumSlidesCollection;
}
