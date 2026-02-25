<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;

class ElysiumCmsSlidesCriteriaEvent extends NestedEvent implements ShopwareSalesChannelEvent
{
    /**
     * @var Criteria
     */
    protected $criteria;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var CmsSlotEntity
     */
    protected $slot;

    /**
     * @param Criteria $criteria
     * @param SalesChannelContext $context
     */
    public function __construct(
        Criteria $criteria,
        SalesChannelContext $context,
        CmsSlotEntity $slot,
    ) {
        $this->criteria = $criteria;
        $this->context = $context;
        $this->slot = $slot;
    }

    public function getSlot(): CmsSlotEntity
    {
        return $this->slot;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
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
