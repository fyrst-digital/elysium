<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class ElysiumSlidesResultEvent extends NestedEvent implements ShopwareSalesChannelEvent
{
    /**
     * @var EntitySearchResult
     */
    protected $result;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    public function __construct(
        EntitySearchResult $result,
        SalesChannelContext $context
    ) {
        $this->result = $result;
        $this->context = $context;
    }

    public function getResult(): EntitySearchResult
    {
        return $this->result;
    }

    public function setResult(EntitySearchResult $result): void
    {
        $this->result = $result;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }
}
