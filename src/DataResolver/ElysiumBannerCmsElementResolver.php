<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel\ElysiumSlideLoader;
use Blur\BlurElysiumSlider\Struct\ElysiumBannerStruct;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumCmsSlidesCriteriaEvent;

class ElysiumBannerCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        private readonly ElysiumSlideLoader $slideLoader,
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

        $criteria = new Criteria();

        $this->eventDispatcher->dispatch(
            new ElysiumCmsSlidesCriteriaEvent($criteria, $context, $slot)
        );

        if (!empty($elysiumSlideId)) {
            $slides = $this->slideLoader->load([$elysiumSlideId], $criteria, $context, "cms-element-elysium-banner-{$slot->getId()}");

            $slide = $slides->get($elysiumSlideId);

            $elysiumBannerStruct->setElysiumSlide($slide);

            $slot->setData($elysiumBannerStruct);
        }
    }
}
