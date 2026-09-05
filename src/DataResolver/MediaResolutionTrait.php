<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Framework\DataAbstractionLayer\EntitySearch;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

trait MediaResolutionTrait
{
    private function resolveMediaForSlides(array $slides, EntityRepository $mediaRepository, SalesChannelContext $context): MediaCollection
    {
        $mediaIds = [];

        /** @var ElysiumSlidesEntity $slide */
        foreach ($slides as $slide) {
            $mediaIds = array_merge($mediaIds, $slide->getContentMediaIds());
        }

        $mediaIds = array_values(array_unique($mediaIds));

        if (empty($mediaIds)) {
            return new MediaCollection([]);
        }

        $criteria = new Criteria($mediaIds);
        $criteria->addAssociation('mediaFolder');
        $criteria->addAssociation('mediaFolder.configuration');
        $criteria->addAssociation('thumbnails');

        /** @var MediaCollection $mediaCollection */
        $mediaCollection = EntitySearch::entities($mediaRepository, $criteria, $context->getContext());

        return $mediaCollection;
    }
}
