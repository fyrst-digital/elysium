<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Struct\ElysiumSliderStruct;

class ElysiumSliderCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @param EntityRepository $elysiumSlidesRepository
     */
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository
    ) {
    }

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
        /** @var ElysiumSliderStruct $elysiumSliderStruct */
        $elysiumSliderStruct = new ElysiumSliderStruct();
        /** @var FieldConfigCollection $fieldConfigCollection */
        $fieldConfigCollection = $slot->getFieldConfig();
        /** @var FieldConfig $elysiumSlideConfig */
        $elysiumSlideConfig = $fieldConfigCollection->get('elysiumSlideCollection');
        /** @var string[] $elysiumSlideIds */
        $elysiumSlideIds = $elysiumSlideConfig->getValue();

        if (!empty($elysiumSlideIds)) {
            $criteria = new Criteria($elysiumSlideIds);
            $criteria->addAssociation('media');

            $slideCollection = $this->elysiumSlidesRepository->search(
                $criteria,
                $resolverContext->getSalesChannelContext()->getContext()
            );

            /** @var ElysiumSlidesEntity[] $elysiumSlides */
            $elysiumSlides = $slideCollection->getElements();

            $elysiumSliderStruct->setSlideCollection($elysiumSlides);
            $slot->setData($elysiumSliderStruct);
        }
    }
}
