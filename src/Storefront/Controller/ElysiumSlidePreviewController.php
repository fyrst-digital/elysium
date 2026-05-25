<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Storefront\Controller;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Preview\PreviewSchemaRegistry;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class ElysiumSlidePreviewController extends StorefrontController
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
        private readonly EntityRepository $salesChannelRepository,
        private readonly PreviewSchemaRegistry $schemaRegistry,
    ) {}

    private function sanitizeOrigin(?string $origin): ?string
    {
        if (empty($origin)) {
            return null;
        }

        $origin = trim($origin);

        if (!filter_var($origin, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parsed = parse_url($origin);
        if (!is_array($parsed) || !isset($parsed['scheme'], $parsed['host'])) {
            return null;
        }

        if (!in_array($parsed['scheme'], ['http', 'https'], true)) {
            return null;
        }

        $port = isset($parsed['port']) ? ':' . (string) $parsed['port'] : '';

        return $parsed['scheme'] . '://' . $parsed['host'] . $port;
    }

    /**
     * @return string[]
     */
    private function getKnownOrigins(Request $request, SalesChannelContext $context): array
    {
        $origins = array_merge(
            [$request->getSchemeAndHttpHost()],
            $this->getAllSalesChannelDomains($context)
        );

        return array_values(array_unique(array_filter($origins)));
    }

    private function validateIframeAccess(Request $request, SalesChannelContext $context): void
    {
        $secFetchDest = $request->headers->get('Sec-Fetch-Dest');

        if ($secFetchDest !== null) {
            if ($secFetchDest !== 'iframe') {
                throw new AccessDeniedHttpException('Preview must be loaded in an iframe.');
            }

            return;
        }

        $referer = $request->headers->get('Referer');
        if ($referer === null) {
            throw new AccessDeniedHttpException('Referer required.');
        }

        $knownOrigins = $this->getKnownOrigins($request, $context);
        $allowed = false;
        foreach ($knownOrigins as $origin) {
            if (str_starts_with($referer, rtrim($origin, '/'))) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            throw new AccessDeniedHttpException('Invalid referer.');
        }
    }

    /**
     * @return string[]
     */
    private function getAllSalesChannelDomains(SalesChannelContext $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        $salesChannels = $this->salesChannelRepository->search($criteria, $context->getContext())->getEntities();

        $domains = [];
        /** @var SalesChannelEntity $salesChannel */
        foreach ($salesChannels as $salesChannel) {
            foreach ($salesChannel->getDomains() ?? [] as $domain) {
                $domains[] = $domain->getUrl();
            }
        }

        return array_values(array_unique(array_filter($domains)));
    }

    private function setPreviewHeaders(Response $response, SalesChannelContext $context, Request $request): Response
    {
        $salesChannelDomains = $this->getAllSalesChannelDomains($context);

        $adminOrigin = null;
        $referer = $request->headers->get('Referer');
        if ($referer !== null) {
            $adminOrigin = $this->sanitizeOrigin($referer);
        }

        $frameAncestors = array_merge(['\'self\''], $salesChannelDomains);
        if ($adminOrigin !== null) {
            $frameAncestors[] = $adminOrigin;
        }
        $frameAncestors = array_unique(array_filter($frameAncestors));

        $response->headers->set('Content-Security-Policy', 'frame-ancestors ' . implode(' ', $frameAncestors));
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }

    #[Route(path: '/elysium-preview/{elementType}/{slideId}', name: 'frontend.elysium-preview.preview', methods: ['GET'], requirements: ['slideId' => '[0-9a-f]{32}'])]
    public function preview(string $elementType, string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $this->validateIframeAccess($request, $context);

        if (!$this->schemaRegistry->has($elementType)) {
            throw $this->createNotFoundException(sprintf('Unknown preview element type "%s".', $elementType));
        }

        $isNewSlide = $request->query->getBoolean('new');
        $slide = $isNewSlide
            ? $this->createEmptySlideForPreview($slideId)
            : $this->loadSlide($slideId, $context);

        $device = $request->query->get('device', 'desktop');
        $layout = $request->query->get('layout', 'detail');

        $response = $this->render('@Storefront/storefront/elysium-slide/preview.html.twig', [
            'slideData' => $slide,
            'device' => $device,
            'layout' => $layout,
            'isNewSlide' => $isNewSlide,
        ]);

        return $this->setPreviewHeaders($response, $context, $request);
    }

    private function createEmptySlideForPreview(string $slideId): ElysiumSlidesEntity
    {
        $slide = new ElysiumSlidesEntity();
        $slide->setId($slideId);

        return $slide;
    }

    private function loadSlide(string $slideId, SalesChannelContext $context): ElysiumSlidesEntity
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

        /** @var ElysiumSlidesEntity|null $slide */
        $slide = $this->elysiumSlidesRepository->search($criteria, $context->getContext())->first();

        if ($slide === null) {
            throw $this->createNotFoundException(sprintf('Slide "%s" not found for preview.', $slideId));
        }

        return $slide;
    }

}
