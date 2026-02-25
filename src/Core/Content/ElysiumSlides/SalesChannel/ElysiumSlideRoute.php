<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesResultEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\StoreApiRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StoreApiRouteScope::ID]])]
class ElysiumSlideRoute extends AbstractElysiumSlideRoute
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function getDecorated(): AbstractElysiumSlideRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/elysium-slide', name: 'store-api.elysium-slide.search', methods: ['GET', 'POST'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function load(Criteria $criteria, SalesChannelContext $context, ?string $identifier = null): ElysiumSlideRouteResponse
    {
        $result = $this->eventDispatcher->dispatch(
            new ElysiumSlidesResultEvent($this->loadSlides($criteria, $context), $context, $identifier)
        );

        /** @var ElysiumSlidesCollection $slides */
        $slides = $result->getResult()->getEntities();

        return new ElysiumSlideRouteResponse($slides);
    }

    #[Route(path: '/store-api/elysium-slide/{slideId}', name: 'store-api.elysium-slide.detail', methods: ['GET'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function loadDetail(string $slideId, SalesChannelContext $context): ElysiumSlideRouteResponse
    {
        $criteria = new Criteria([$slideId]);
        $result = $this->loadSlides($criteria, $context);

        return new ElysiumSlideRouteResponse($result->getEntities());
    }

    private function loadSlides(Criteria $criteria, SalesChannelContext $context): EntitySearchResult
    {
        $this->addAssociations($criteria);

        return $this->elysiumSlidesRepository->search(
            $criteria,
            $context->getContext()
        );
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
