<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

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
        private readonly ElysiumSlideLoader $slideLoader,
    ) {}

    public function getDecorated(): AbstractElysiumSlideRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/elysium-slide', name: 'store-api.elysium-slide.search', methods: ['GET', 'POST'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function load(Criteria $criteria, SalesChannelContext $context): ElysiumSlideRouteResponse
    {
        $slideIds = $criteria->getIds();
        $slides = $this->slideLoader->load($slideIds, $criteria, $context);

        return new ElysiumSlideRouteResponse($slides);
    }

    #[Route(path: '/store-api/elysium-slide/{slideId}', name: 'store-api.elysium-slide.detail', methods: ['GET'], defaults: ['_entity' => 'blur_elysium_slides'])]
    public function loadDetail(string $slideId, SalesChannelContext $context): ElysiumSlideRouteResponse
    {
        $criteria = new Criteria([$slideId]);
        $slides = $this->slideLoader->load([$slideId], $criteria, $context);

        return new ElysiumSlideRouteResponse($slides);
    }
}