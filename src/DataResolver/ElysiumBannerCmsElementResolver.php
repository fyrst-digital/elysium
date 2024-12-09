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
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Struct\ElysiumBannerStruct;

class ElysiumBannerCmsElementResolver extends AbstractCmsElementResolver
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
        return 'blur-elysium-banner';
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
        $elysiumBannerStruct = new ElysiumBannerStruct();
        /** @var FieldConfigCollection $fieldConfigCollection */
        $fieldConfigCollection = $slot->getFieldConfig();
        /** @var string $elysiumSlideId */
        $elysiumSlideId = $fieldConfigCollection->get('elysiumSlide')?->getValue() ?? '';

        if (!empty($elysiumSlideId)) {
            $criteria = new Criteria([$elysiumSlideId]);
            $criteria->addAssociation('media');
            $criteria->addAssociation('media.mediaFolder');
            $criteria->addAssociation('media.mediaFolder.configuration');
            $criteria->addAssociation('product.media');
            $criteria->addAssociation('product.cover');

            /** @var EntitySearchResult<ElysiumSlidesCollection> $elysiumSlideResult */
            $elysiumSlideResult = $this->elysiumSlidesRepository->search(
                $criteria,
                $resolverContext->getSalesChannelContext()->getContext()
            );

            /** @var ElysiumSlidesEntity $elysiumSlide */
            $elysiumSlide = $elysiumSlideResult->first();

            $elysiumBannerStruct->setElysiumSlide($elysiumSlide);

            $slot->setData($elysiumBannerStruct);
        }
    }
}
