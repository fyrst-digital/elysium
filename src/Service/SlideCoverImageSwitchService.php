<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;

class SlideCoverImageSwitchService
{
    public function __construct(
        private readonly EntityRepository $slideRepository
    ) {
    }

    public function switch(Context $context): int
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('slideCoverMobileId', null)
        );
        $criteria->addFilter(
            new NotFilter(NotFilter::CONNECTION_AND, [
                new EqualsFilter('slideCoverId', null),
            ])
        );

        $result = $this->slideRepository->search($criteria, $context);

        if ($result->getTotal() === 0) {
            return 0;
        }

        $payload = [];
        foreach ($result->getEntities() as $slide) {
            $payload[] = [
                'id' => $slide->getId(),
                'slideCoverMobileId' => $slide->getSlideCoverId(),
                'slideCoverId' => null,
            ];
        }

        $this->slideRepository->upsert($payload, $context);

        return $result->getTotal();
    }
}
