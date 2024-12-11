<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Struct\ElysiumSliderStruct;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesResultEvent;

class ElysiumSliderCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @param EntityRepository<ElysiumSlidesCollection> $elysiumSlidesRepository
     */
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
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
            $criteria = new Criteria($elysiumSlideIds);
            $criteria->addAssociation('media');
            /**
             * @todo only set association if the right linking type is set
             */
            $criteria->addAssociation('product');
            $criteria->addAssociation('product.media');
            $criteria->addAssociation('product.cover');

            $slideCollection = $this->eventDispatcher->dispatch(
                new ElysiumSlidesResultEvent($this->elysiumSlidesRepository->search(
                    $criteria,
                    $context->getContext()
                ), $slot, $context)
            );

            /** @var ElysiumSlidesEntity[] $elysiumSlides */
            $elysiumSlides = $slideCollection->getResult()->getElements();
            $elysiumSliderStruct->setSlideCollection($elysiumSlides);
            $slot->setData($elysiumSliderStruct);
        }
    }
}
