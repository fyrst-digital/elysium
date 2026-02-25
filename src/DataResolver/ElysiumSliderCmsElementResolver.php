<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel\ElysiumSlideLoader;
use Blur\BlurElysiumSlider\Struct\ElysiumSliderStruct;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

class ElysiumSliderCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        private readonly ElysiumSlideLoader $slideLoader,
    ) {}

    public function getType(): string
    {
        return 'blur-elysium-slider';
    }

    public function collect(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext
    ): ?CriteriaCollection {
        return null;
    }

    public function enrich(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext,
        ElementDataCollection $result
    ): void {
        $context = $resolverContext->getSalesChannelContext();
        /** @var ElysiumSliderStruct $elysiumSliderStruct */
        $elysiumSliderStruct = new ElysiumSliderStruct();
        /** @var FieldConfigCollection $fieldConfigCollection */
        $fieldConfigCollection = $slot->getFieldConfig();
        /** @var FieldConfig $elysiumSlideConfig */
        $elysiumSlideConfig = $fieldConfigCollection->get('elysiumSlideCollection');
        /** @var string[] $elysiumSlideIds */
        $elysiumSlideIds = $elysiumSlideConfig->getValue();

        if (!empty($elysiumSlideIds)) {
            $slides = $this->slideLoader->load($elysiumSlideIds, null, $context);

            $elysiumSliderStruct->setSlideCollection($slides->getElements());
            $slot->setData($elysiumSliderStruct);
        }
    }
}