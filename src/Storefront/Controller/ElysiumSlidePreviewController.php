<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Storefront\Controller;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class ElysiumSlidePreviewController extends StorefrontController
{
    public function __construct(
        private readonly EntityRepository $elysiumSlidesRepository,
        private readonly EntityRepository $productRepository,
        private readonly EntityRepository $salesChannelRepository,
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

    private function setPreviewHeaders(Response $response, SalesChannelContext $context, array $adminOrigin): Response
    {
        $salesChannelDomains = $this->getAllSalesChannelDomains($context);
        $frameAncestors = array_merge(['\'self\''], $salesChannelDomains, $adminOrigin);
        $frameAncestors = array_unique(array_filter($frameAncestors));

        $response->headers->set('Content-Security-Policy', 'frame-ancestors ' . implode(' ', $frameAncestors));

        return $response;
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

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
    }

    #[Route(path: '/elysium-slide/preview/headline/{slideId}', name: 'frontend.elysium-slide.preview.headline', methods: ['POST'])]
    public function headline(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/headline.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
    }

    #[Route(path: '/elysium-slide/preview/button/{slideId}', name: 'frontend.elysium-slide.preview.button', methods: ['POST'])]
    public function button(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/button.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
    }

    #[Route(path: '/elysium-slide/preview/cover/{slideId}', name: 'frontend.elysium-slide.preview.cover', methods: ['POST'])]
    public function cover(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/cover.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
    }

    #[Route(path: '/elysium-slide/preview/focus-image/{slideId}', name: 'frontend.elysium-slide.preview.focus-image', methods: ['POST'])]
    public function focusImage(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/focus-image.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
    }

    #[Route(path: '/elysium-slide/preview/description/{slideId}', name: 'frontend.elysium-slide.preview.description', methods: ['POST'])]
    public function description(string $slideId, SalesChannelContext $context, Request $request): Response
    {
        $slide = $this->loadAndMergeSlide($slideId, $context, $request);
        $device = $request->query->get('device', 'desktop');
        $adminOrigin = $this->parseAdminOrigin($request->query->get('adminOrigin', $request->getSchemeAndHttpHost()));

        $response = $this->render('@Storefront/storefront/elysium-slide/preview/description.html.twig', [
            'slideData' => $slide,
            'device' => $device,
        ]);

        return $this->setPreviewHeaders($response, $context, $adminOrigin);
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
            $slide = $this->mergeSlideData($slide, $postData['slide'], $context);
        }

        return $slide;
    }

    private function mergeSlideData(ElysiumSlidesEntity $entity, array $postData, SalesChannelContext $context): ElysiumSlidesEntity
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

        if (isset($postData['presentationMedia']) && is_array($postData['presentationMedia'])) {
            $media = $this->createMediaEntityFromArray($postData['presentationMedia']);
            $entity->setPresentationMedia($media);
            $entity->setPresentationMediaId($media->getId());
        } elseif (array_key_exists('presentationMedia', $postData) && $postData['presentationMedia'] === null) {
            $entity->setPresentationMedia(null);
            $entity->setPresentationMediaId(null);
        }

        if (array_key_exists('productId', $postData)) {
            $newProductId = $postData['productId'] ?: null;
            $entity->setProductId($newProductId);

            if ($newProductId !== null) {
                $product = $this->loadProduct($newProductId, $context);
                $entity->setProduct($product);
            } else {
                $entity->setProduct(null);
            }
        }

        $this->mergeCoverMedia($entity, $postData);

        return $entity;
    }

    private function mergeCoverMedia(ElysiumSlidesEntity $entity, array $postData): void
    {
        $mappings = [
            'slideCover' => ['setSlideCover', 'setSlideCoverId'],
            'slideCoverMobile' => ['setSlideCoverMobile', 'setSlideCoverMobileId'],
            'slideCoverTablet' => ['setSlideCoverTablet', 'setSlideCoverTabletId'],
            'slideCoverVideo' => ['setSlideCoverVideo', 'setSlideCoverVideoId'],
        ];

        foreach ($mappings as $key => [$setter, $idSetter]) {
            if (isset($postData[$key]) && is_array($postData[$key])) {
                $media = $this->createMediaEntityFromArray($postData[$key]);
                $entity->$setter($media);
                $entity->$idSetter($media->getId());
            } elseif (array_key_exists($key, $postData) && $postData[$key] === null) {
                $entity->$setter(null);
                $entity->$idSetter(null);
            }
        }
    }

    private function loadProduct(string $productId, SalesChannelContext $context): ?ProductEntity
    {
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('cover');
        $criteria->addAssociation('cover.media');

        /** @var ProductEntity|null $product */
        $product = $this->productRepository->search($criteria, $context->getContext())->first();

        return $product;
    }

    private function createMediaEntityFromArray(array $data): MediaEntity
    {
        $media = new MediaEntity();
        $media->setId($data['id'] ?? '');

        if (isset($data['url'])) {
            $media->setUrl($data['url']);
        }

        if (isset($data['mimeType'])) {
            $media->setMimeType($data['mimeType']);
        }

        if (isset($data['fileExtension'])) {
            $media->setFileExtension($data['fileExtension']);
        }

        if (isset($data['fileName'])) {
            $media->setFileName($data['fileName']);
        }

        if (isset($data['path'])) {
            $media->setPath($data['path']);
        }

        if (isset($data['metaData']) && is_array($data['metaData'])) {
            $media->setMetaData($data['metaData']);
        }

        if (isset($data['alt'])) {
            $media->setAlt($data['alt']);
        }

        if (isset($data['title'])) {
            $media->setTitle($data['title']);
        }

        if (isset($data['thumbnails']) && is_array($data['thumbnails'])) {
            $thumbnails = [];
            foreach ($data['thumbnails'] as $thumbData) {
                if (!is_array($thumbData) || !isset($thumbData['id'])) {
                    continue;
                }
                $thumb = new MediaThumbnailEntity();
                $thumb->setId($thumbData['id']);
                $thumb->setUniqueIdentifier($thumbData['id']);

                if (isset($thumbData['width'])) {
                    $thumb->setWidth((int) $thumbData['width']);
                }
                if (isset($thumbData['height'])) {
                    $thumb->setHeight((int) $thumbData['height']);
                }
                if (isset($thumbData['url'])) {
                    $thumb->setUrl($thumbData['url']);
                }
                if (isset($thumbData['path'])) {
                    $thumb->setPath($thumbData['path']);
                }

                $thumbnails[] = $thumb;
            }

            if ($thumbnails !== []) {
                $media->setThumbnails(new MediaThumbnailCollection($thumbnails));
            }
        }

        return $media;
    }
}
