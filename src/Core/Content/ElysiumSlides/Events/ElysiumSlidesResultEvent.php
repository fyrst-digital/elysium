<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ElysiumSlidesResultEvent extends NestedEvent implements ShopwareSalesChannelEvent
{
    /**
     * @var ElysiumSlidesCollection
     */
    protected $result;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var ?string
     */
    protected $identifier;

    public function __construct(
        ElysiumSlidesCollection $result,
        SalesChannelContext $context,
        ?string $identifier = null
    ) {
        $this->result = $result;
        $this->context = $context;
        $this->identifier = $identifier;
    }

    public function getResult(): ElysiumSlidesCollection
    {
        return $this->result;
    }

    public function setResult(ElysiumSlidesCollection $result): void
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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}
