/* eslint-disable */
// This file is auto-generated. Do not edit manually.
// Run `bin/console elysium:preview-schema:generate` to regenerate.
import type { PreviewSchema } from '../composables/preview-schema';

export const previewSchema: PreviewSchema = {
    "elementType": "blur-elysium-slide",
    "fieldMappings": [
        {
            "path": "slide.productId",
            "fields": [
                "productId"
            ]
        },
        {
            "path": "slide.categoryId",
            "fields": [
                "categoryId"
            ]
        },
        {
            "path": "slide.contentSettings",
            "fields": [
                "contentSettings"
            ],
            "deep": true
        },
        {
            "path": "slide.slideSettings.slide.headline.element",
            "fields": [
                "headlineElement"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.type",
            "fields": [
                "linkingType"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showProductTitle",
            "fields": [
                "showProductTitle"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showProductDescription",
            "fields": [
                "showProductDescription"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showProductFocusImage",
            "fields": [
                "showProductFocusImage"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showCategoryTitle",
            "fields": [
                "showCategoryTitle"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showCategoryDescription",
            "fields": [
                "showCategoryDescription"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.showCategoryFocusImage",
            "fields": [
                "showCategoryFocusImage"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.buttonAppearance",
            "fields": [
                "buttonAppearance"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.buttonSize",
            "fields": [
                "buttonSize"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.overlay",
            "fields": [
                "linkingOverlay"
            ]
        },
        {
            "path": "slide.slideSettings.slide.linking.openExternal",
            "fields": [
                "linkingOpenExternal"
            ]
        },
        {
            "path": "slide.slideSettings.slide.cssClass",
            "fields": [
                "slideCssClass"
            ]
        },
        {
            "path": "slide.slideSettings.slide.bgColor",
            "fields": [
                "slideBgColor"
            ]
        },
        {
            "path": "slide.slideSettings.slide.bgGradient",
            "fields": [
                "slideBgGradient"
            ],
            "deep": true
        },
        {
            "path": "slide.slideSettings.slide.headline.color",
            "fields": [
                "headlineColor"
            ]
        },
        {
            "path": "slide.slideSettings.slide.description.color",
            "fields": [
                "descriptionColor"
            ]
        },
        {
            "path": "slide.slideSettings.container.bgColor",
            "fields": [
                "containerBgColor"
            ]
        },
        {
            "path": "slide.slideSettings.viewports",
            "fields": [
                "viewports"
            ],
            "deep": true
        },
        {
            "path": "device",
            "fields": [
                "device"
            ]
        },
        {
            "path": "aspectRatioX",
            "fields": [
                "previewSizing"
            ]
        },
        {
            "path": "aspectRatioY",
            "fields": [
                "previewSizing"
            ]
        },
        {
            "path": "maxWidth",
            "fields": [
                "previewSizing"
            ]
        }
    ],
    "fragments": [
        {
            "name": "styles",
            "mode": "css-variable",
            "watchedFields": [
                "slideBgColor",
                "slideBgGradient",
                "headlineColor",
                "descriptionColor",
                "containerBgColor",
                "viewports",
                "device",
                "slideCssClass"
            ],
            "template": null,
            "domSelector": null,
            "insertStrategy": null,
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "sizing",
            "mode": "preview-sizing",
            "watchedFields": [
                "previewSizing"
            ],
            "template": null,
            "domSelector": null,
            "insertStrategy": null,
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "headline",
            "mode": "partial",
            "watchedFields": [
                "contentSettings",
                "headlineElement",
                "showProductTitle",
                "showCategoryTitle",
                "linkingType",
                "productId",
                "categoryId"
            ],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/headline.html.twig",
            "domSelector": "[data-elysium-slide-headline]",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "afterbegin"
        },
        {
            "name": "description",
            "mode": "partial",
            "watchedFields": [
                "contentSettings",
                "showProductDescription",
                "showCategoryDescription",
                "linkingType",
                "productId",
                "categoryId"
            ],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/description.html.twig",
            "domSelector": "[data-elysium-slide-description]",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "beforeend"
        },
        {
            "name": "button",
            "mode": "partial",
            "watchedFields": [
                "contentSettings",
                "buttonAppearance",
                "buttonSize",
                "linkingOverlay",
                "linkingOpenExternal",
                "linkingType"
            ],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/button.html.twig",
            "domSelector": ".blur-elysium-slide-actions",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "beforeend"
        },
        {
            "name": "cover",
            "mode": "partial",
            "watchedFields": [
                "contentSettings",
                "showProductFocusImage",
                "showCategoryFocusImage",
                "linkingType"
            ],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/cover.html.twig",
            "domSelector": ".blur-elysium-slide-cover-picture, .blur-elysium-slide-cover-video",
            "insertStrategy": "replace-inner",
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "focus-image",
            "mode": "partial",
            "watchedFields": [
                "contentSettings",
                "showProductFocusImage",
                "showCategoryFocusImage",
                "productId",
                "categoryId",
                "linkingType"
            ],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/image.html.twig",
            "domSelector": "[data-elysium-slide-focus-image]",
            "insertStrategy": "replace",
            "fallbackContainer": "[data-elysium-slide-container]",
            "fallbackPosition": "beforeend"
        }
    ]
} as PreviewSchema;
