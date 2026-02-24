<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ElysiumSlideLoader
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
    ) {}

    public function load(
        array $slideIds,
        ?Criteria $criteria = null,
        SalesChannelContext $context = null
    ): ElysiumSlidesCollection {
        $criteria ??= new Criteria();

        if (!empty($slideIds)) {
            $criteria->setIds($slideIds);
        }

        $this->addAssociations($criteria);

        $result = $this->elysiumSlidesRepository->search(
            $criteria,
            $context?->getContext()
        );

        return new ElysiumSlidesCollection($result->getEntities()->getElements());
    }

    private function addAssociations(Criteria $criteria): void
    {
        $criteria->addAssociation('media');
        $criteria->addAssociation('media.mediaFolder');
        $criteria->addAssociation('media.mediaFolder.configuration');
        $criteria->addAssociation('product');
        $criteria->addAssociation('product.media');
        $criteria->addAssociation('product.cover');
        $criteria->addAssociation('product.cover.media');
        $criteria->addAssociation('category');
        $criteria->addAssociation('category.media');
    }
}