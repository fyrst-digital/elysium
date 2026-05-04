<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Storefront\Controller;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class ElysiumSlidePreviewController extends StorefrontController
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
    ) {}

    /**
     * @return string[]
     */
    private function parseAdminOrigin(string $raw): array
    {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [$raw];
    }

    #[Route(path: '/elysium-slide/preview/{slideId}', name: 'frontend.elysium-slide.preview', methods: ['GET'])]
    public function preview(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadSlide($slideId, $context);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview.html.twig', [
            'slideData' => $slide,
            'device' => $device,
            'adminOrigin' => $adminOrigin,
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }

    #[Route(path: '/elysium-slide/preview/headline/{slideId}', name: 'frontend.elysium-slide.preview.headline', methods: ['POST'])]
    public function headline(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/headline.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }

    #[Route(path: '/elysium-slide/preview/button/{slideId}', name: 'frontend.elysium-slide.preview.button', methods: ['POST'])]
    public function button(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/button.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }

    #[Route(path: '/elysium-slide/preview/cover/{slideId}', name: 'frontend.elysium-slide.preview.cover', methods: ['POST'])]
    public function cover(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/cover.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
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
            throw new NotFoundHttpException('Slide not found');
        }

        return $slide;
    }

    private function loadAndMergeSlide(string $slideId, SalesChannelContext $context, Request $request): ElysiumSlidesEntity
    {
        $slide = $this->loadSlide($slideId, $context);

        $postData = json_decode($request->getContent(), true);
        if (isset($postData['slide']) && is_array($postData['slide'])) {
            $slide = $this->mergeSlideData($slide, $postData['slide']);
        }

        return $slide;
    }

    private function mergeSlideData(ElysiumSlidesEntity $entity, array $postData): ElysiumSlidesEntity
    {
        if (isset($postData['title'])) {
            $entity->setTitle($postData['title']);
        }

        if (isset($postData['description'])) {
            $entity->setDescription($postData['description']);
        }

        if (isset($postData['buttonLabel'])) {
            $entity->setButtonLabel($postData['buttonLabel']);
        }

        if (isset($postData['url'])) {
            $entity->setUrl($postData['url']);
        }

        if (isset($postData['slideSettings']) && is_array($postData['slideSettings'])) {
            $mergedSettings = array_replace_recursive(
                $entity->getSlideSettings() ?? [],
                $postData['slideSettings']
            );
            $entity->setSlideSettings($mergedSettings);
        }

        if (isset($postData['contentSettings']) && is_array($postData['contentSettings'])) {
            $mergedContentSettings = array_replace_recursive(
                $entity->getContentSettings() ?? [],
                $postData['contentSettings']
            );
            $entity->setContentSettings($mergedContentSettings);
        }

        return $entity;
    }
}
