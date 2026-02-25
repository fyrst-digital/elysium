<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\StoreApiRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StoreApiRouteScope::ID]])]
class ElysiumSlideRoute extends AbstractElysiumSlideRoute
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
    ) {}

    public function getDecorated(): AbstractElysiumSlideRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/elysium-slide', name: 'store-api.elysium-slide.search', methods: ['GET', 'POST'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function load(Criteria $criteria, SalesChannelContext $context): ElysiumSlideRouteResponse
    {
        $slides = $this->loadSlides($criteria, $context);

        return new ElysiumSlideRouteResponse($slides);
    }

    #[Route(path: '/store-api/elysium-slide/{slideId}', name: 'store-api.elysium-slide.detail', methods: ['GET'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function loadDetail(string $slideId, SalesChannelContext $context): ElysiumSlideRouteResponse
    {
        $criteria = new Criteria([$slideId]);
        $slides = $this->loadSlides($criteria, $context);

        return new ElysiumSlideRouteResponse($slides);
    }

    private function loadSlides(Criteria $criteria, SalesChannelContext $context): ElysiumSlidesCollection
    {
        $this->addAssociations($criteria);

        $result = $this->elysiumSlidesRepository->search(
            $criteria,
            $context->getContext()
        );

        return $result->getEntities();
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
