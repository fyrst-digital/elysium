<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Struct\ElysiumBannerStruct;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumCmsSlidesCriteriaEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumCmsSlidesResultEvent;

class ElysiumBannerCmsElementResolver extends AbstractCmsElementResolver
{

    private const EVENT_ID = 'cms-element-elysium-banner';

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
            /**
             * @todo #76
             * - only set association if the right linking type is set
             * - create and include a centralized slide loader 
             */
            $criteria->addAssociation('media');
            $criteria->addAssociation('media.mediaFolder');
            $criteria->addAssociation('media.mediaFolder.configuration');
            $criteria->addAssociation('product');
            $criteria->addAssociation('product.media');
            $criteria->addAssociation('product.cover');
            $criteria->addAssociation('product.cover.media');

            $this->eventDispatcher->dispatch(
                new ElysiumCmsSlidesCriteriaEvent($criteria, $context, $slot, self::EVENT_ID)
            );

            /** @var ElysiumCmsSlidesResultEvent $elysiumSlideResult */
            $elysiumSlideResult = $this->eventDispatcher->dispatch(
                new ElysiumCmsSlidesResultEvent($this->elysiumSlidesRepository->search(
                    $criteria,
                    $context->getContext()
                ), $context, $slot, self::EVENT_ID)
            );

            /** @var ElysiumSlidesEntity $elysiumSlide */
            $elysiumSlide = $elysiumSlideResult->getResult()->first();

            $elysiumBannerStruct->setElysiumSlide($elysiumSlide);

            $slot->setData($elysiumBannerStruct);
        }
    }
}
