<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesCriteriaEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;

class ElysiumCmsSlidesCriteriaEvent extends ElysiumSlidesCriteriaEvent
{
    /**
     * @var CmsSlotEntity
     */
    protected $slot;

    /**
     * @param Criteria $criteria
     * @param SalesChannelContext $context
     * @param string|null $identifier
     */
    public function __construct(
        Criteria $criteria,
        SalesChannelContext $context,
        CmsSlotEntity $slot,
        ?string $identifier = null
    ) {
        parent::__construct($criteria, $context, $identifier);
        $this->slot = $slot;
    }

    public function getSlot(): CmsSlotEntity
    {
        return $this->slot;
    }
}
