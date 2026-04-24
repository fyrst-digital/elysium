<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class ElysiumSlidePreviewController extends StorefrontController
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
    ) {}

    #[Route(path: '/elysium-slide/preview/{slideId}', name: 'frontend.elysium-slide.preview', methods: ['GET'])]
    public function preview(string $slideId, SalesChannelContext $context): Response
    {
        $criteria = new Criteria([$slideId]);
        $criteria->addAssociation('slideCover');
        $criteria->addAssociation('slideCoverMobile');
        $criteria->addAssociation('slideCoverTablet');
        $criteria->addAssociation('slideCoverVideo');
        $criteria->addAssociation('presentationMedia');
        $criteria->addAssociation('product');
        $criteria->addAssociation('product.cover');
        $criteria->addAssociation('product.cover.media');
        $criteria->addAssociation('category');

        $slide = $this->elysiumSlidesRepository->search($criteria, $context->getContext())->first();

        if ($slide === null) {
            throw new NotFoundHttpException('Slide not found');
        }

        $response = $this->render('@Storefront/storefront/elysium-slide/preview.html.twig', [
            'slideData' => $slide,
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
