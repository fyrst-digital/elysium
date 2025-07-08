<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesResultEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;

class ElysiumCmsSlidesResultEvent extends ElysiumSlidesResultEvent
{
    /**
     * @var CmsSlotEntity
     */
    protected $slot;

    /**
     * @param EntitySearchResult $result
     * @param SalesChannelContext $context
     * @param string|null $identifier
     */
    public function __construct(
        EntitySearchResult $result,
        SalesChannelContext $context,
        CmsSlotEntity $slot,
        ?string $identifier = null
    ) {
        parent::__construct($result, $context, $identifier);
        $this->slot = $slot;
    }

    public function getSlot(): CmsSlotEntity
    {
        return $this->slot;
    }
}
