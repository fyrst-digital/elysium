<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

use Blur\BlurElysiumSlider\Struct\ElysiumSliderStruct;

class ElysiumSliderCmsElementResolver extends AbstractCmsElementResolver
{
    public $blurElysiumSlides;

    public function __construct(
        $blurElysiumSlides
    )
    {
        $this->blurElysiumSlides = $blurElysiumSlides;
    }

    public function getType(): string
    {
        return 'blur-elysium-slider';
    }

    public function collect( 
        CmsSlotEntity $slot, 
        ResolverContext $resolverContext

    ): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(
        CmsSlotEntity $slot, 
        ResolverContext $resolverContext, 
        ElementDataCollection $result
        
    ): void
    {
        $elysiumSliderStruct = new ElysiumSliderStruct();

        $criteria = new Criteria( $slot->getFieldConfig()->get( 'elysiumSlideCollection' )->getValue() );
        $criteria->addAssociation( 'media' );

        $slideCollection = $this->blurElysiumSlides->search(
            $criteria,
            $resolverContext->getSalesChannelContext()->getContext()
        );

        $elysiumSliderStruct->setSlideCollection( $slideCollection->getElements() );

        $slot->setData( $elysiumSliderStruct );
    }
}