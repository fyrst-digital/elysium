<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Preview;

/**
 * Registry for preview schemas by element type.
 */
class PreviewSchemaRegistry
{
    /** @var array<string, PreviewSchema> */
    private array $schemas = [];

    public function __construct()
    {
        $this->registerSlideSchema();
    }

    public function register(string $elementType, PreviewSchema $schema): void
    {
        $this->schemas[$elementType] = $schema;
    }

    public function get(string $elementType): ?PreviewSchema
    {
        return $this->schemas[$elementType] ?? null;
    }

    public function has(string $elementType): bool
    {
        return isset($this->schemas[$elementType]);
    }

    private function registerSlideSchema(): void
    {
        $fieldMappings = [
            ['path' => 'slide.productId', 'fields' => ['productId']],
            ['path' => 'slide.categoryId', 'fields' => ['categoryId']],
            ['path' => 'slide.contentSettings.title', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.description', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.button.label', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.url', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.mobileId', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.tabletId', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.desktopId', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.videoId', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.alt', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.slideCover.title', 'fields' => ['contentSettings']],
            ['path' => 'slide.contentSettings.focusImageId', 'fields' => ['contentSettings']],
            ['path' => 'slide.slideSettings.slide.headline.element', 'fields' => ['headlineElement']],
            ['path' => 'slide.slideSettings.slide.linking.type', 'fields' => ['linkingType']],
            ['path' => 'slide.slideSettings.slide.linking.showProductTitle', 'fields' => ['showProductTitle']],
            ['path' => 'slide.slideSettings.slide.linking.showProductDescription', 'fields' => ['showProductDescription']],
            ['path' => 'slide.slideSettings.slide.linking.showProductFocusImage', 'fields' => ['showProductFocusImage']],
            ['path' => 'slide.slideSettings.slide.linking.showCategoryTitle', 'fields' => ['showCategoryTitle']],
            ['path' => 'slide.slideSettings.slide.linking.showCategoryDescription', 'fields' => ['showCategoryDescription']],
            ['path' => 'slide.slideSettings.slide.linking.showCategoryFocusImage', 'fields' => ['showCategoryFocusImage']],
            ['path' => 'slide.slideSettings.slide.linking.buttonAppearance', 'fields' => ['buttonAppearance']],
            ['path' => 'slide.slideSettings.slide.linking.buttonSize', 'fields' => ['buttonSize']],
            ['path' => 'slide.slideSettings.slide.linking.overlay', 'fields' => ['linkingOverlay']],
            ['path' => 'slide.slideSettings.slide.linking.openExternal', 'fields' => ['linkingOpenExternal']],
            ['path' => 'slide.slideSettings.slide.cssClass', 'fields' => ['slideCssClass']],
            ['path' => 'slide.slideSettings.slide.bgColor', 'fields' => ['slideBgColor']],
            ['path' => 'slide.slideSettings.slide.bgGradient', 'fields' => ['slideBgGradient'], 'deep' => true],
            ['path' => 'slide.slideSettings.slide.headline.color', 'fields' => ['headlineColor']],
            ['path' => 'slide.slideSettings.slide.description.color', 'fields' => ['descriptionColor']],
            ['path' => 'slide.slideSettings.container.bgColor', 'fields' => ['containerBgColor']],
            ['path' => 'slide.slideSettings.viewports', 'fields' => ['viewports'], 'deep' => true],
            ['path' => 'device', 'fields' => ['device']],
            ['path' => 'aspectRatioX', 'fields' => ['previewSizing']],
            ['path' => 'aspectRatioY', 'fields' => ['previewSizing']],
            ['path' => 'maxWidth', 'fields' => ['previewSizing']],
        ];

        $fragments = [
            new PreviewFragment(
                'styles',
                'css-variable',
                ['slideBgColor', 'slideBgGradient', 'headlineColor', 'descriptionColor', 'containerBgColor', 'viewports', 'device', 'slideCssClass']
            ),
            new PreviewFragment(
                'sizing',
                'preview-sizing',
                ['previewSizing']
            ),
            new PreviewFragment(
                'headline',
                'partial',
                ['contentSettings', 'headlineElement', 'showProductTitle', 'showCategoryTitle', 'linkingType', 'productId', 'categoryId'],
                '@Storefront/storefront/component/blur-elysium-slide/includes/headline.html.twig',
                '[data-elysium-slide-headline]',
                'replace',
                '.blur-elysium-slide-content',
                'afterbegin'
            ),
            new PreviewFragment(
                'description',
                'partial',
                ['contentSettings', 'showProductDescription', 'showCategoryDescription', 'linkingType', 'productId', 'categoryId'],
                '@Storefront/storefront/component/blur-elysium-slide/includes/description.html.twig',
                '[data-elysium-slide-description]',
                'replace',
                '.blur-elysium-slide-content',
                'beforeend'
            ),
            new PreviewFragment(
                'button',
                'partial',
                ['contentSettings', 'buttonAppearance', 'buttonSize', 'linkingOverlay', 'linkingOpenExternal', 'linkingType'],
                '@Storefront/storefront/component/blur-elysium-slide/includes/button.html.twig',
                '.blur-elysium-slide-actions',
                'replace',
                '.blur-elysium-slide-content',
                'beforeend'
            ),
            new PreviewFragment(
                'cover',
                'partial',
                ['contentSettings', 'device', 'showProductFocusImage', 'showCategoryFocusImage', 'linkingType'],
                '@Storefront/storefront/component/blur-elysium-slide/includes/cover.html.twig',
                '.blur-elysium-slide-cover-picture, .blur-elysium-slide-cover-video',
                'replace-inner',
                null,
                null
            ),
            new PreviewFragment(
                'focus-image',
                'partial',
                ['contentSettings', 'showProductFocusImage', 'showCategoryFocusImage', 'productId', 'categoryId', 'linkingType'],
                '@Storefront/storefront/component/blur-elysium-slide/includes/image.html.twig',
                '[data-elysium-slide-focus-image]',
                'replace',
                '[data-elysium-slide-container]',
                'beforeend'
            ),
        ];

        $this->register('blur-elysium-slide', new PreviewSchema('blur-elysium-slide', $fieldMappings, $fragments));
    }
}
