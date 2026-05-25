/* eslint-disable */
// This file is auto-generated. Do not edit manually.
// Run `bin/console elysium:preview-schema:generate` to regenerate.
export const previewSchema = {
    "elementType": "blur-elysium-slide",
    "fieldMappings": [
        {"path": "slide.title", "fields": ["title"]},
        {"path": "slide.description", "fields": ["description"]},
        {"path": "slide.buttonLabel", "fields": ["buttonLabel"]},
        {"path": "slide.url", "fields": ["url"]},
        {"path": "slide.presentationMedia", "fields": ["presentationMedia"], "deep": true},
        {"path": "slide.productId", "fields": ["productId"]},
        {"path": "slide.contentSettings", "fields": ["contentSettings"], "deep": true},
        {"path": "slide.slideCover", "fields": ["slideCover"], "deep": true},
        {"path": "slide.slideCoverMobile", "fields": ["slideCoverMobile"], "deep": true},
        {"path": "slide.slideCoverTablet", "fields": ["slideCoverTablet"], "deep": true},
        {"path": "slide.slideCoverVideo", "fields": ["slideCoverVideo"], "deep": true},
        {"path": "slide.slideSettings.slide.headline.element", "fields": ["headlineElement"]},
        {"path": "slide.slideSettings.slide.linking.type", "fields": ["linkingType"]},
        {"path": "slide.slideSettings.slide.linking.showProductTitle", "fields": ["showProductTitle"]},
        {"path": "slide.slideSettings.slide.linking.showProductDescription", "fields": ["showProductDescription"]},
        {"path": "slide.slideSettings.slide.linking.showProductFocusImage", "fields": ["showProductFocusImage"]},
        {"path": "slide.slideSettings.slide.linking.buttonAppearance", "fields": ["buttonAppearance"]},
        {"path": "slide.slideSettings.slide.linking.buttonSize", "fields": ["buttonSize"]},
        {"path": "slide.slideSettings.slide.linking.overlay", "fields": ["linkingOverlay"]},
        {"path": "slide.slideSettings.slide.linking.openExternal", "fields": ["linkingOpenExternal"]},
        {"path": "slide.slideSettings.slide.cssClass", "fields": ["slideCssClass"]},
        {"path": "slide.slideSettings.slide.bgColor", "fields": ["slideBgColor"]},
        {"path": "slide.slideSettings.slide.bgGradient", "fields": ["slideBgGradient"], "deep": true},
        {"path": "slide.slideSettings.slide.headline.color", "fields": ["headlineColor"]},
        {"path": "slide.slideSettings.slide.description.color", "fields": ["descriptionColor"]},
        {"path": "slide.slideSettings.container.bgColor", "fields": ["containerBgColor"]},
        {"path": "slide.slideSettings.viewports", "fields": ["viewports"], "deep": true},
        {"path": "device", "fields": ["device"]},
        {"path": "aspectRatioX", "fields": ["previewSizing"]},
        {"path": "aspectRatioY", "fields": ["previewSizing"]},
        {"path": "maxWidth", "fields": ["previewSizing"]}
    ],
    "fragments": [
        {
            "name": "styles",
            "mode": "css-variable",
            "watchedFields": ["slideBgColor", "slideBgGradient", "headlineColor", "descriptionColor", "containerBgColor", "viewports", "device", "slideCssClass"],
            "template": null,
            "domSelector": null,
            "insertStrategy": null,
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "sizing",
            "mode": "preview-sizing",
            "watchedFields": ["previewSizing"],
            "template": null,
            "domSelector": null,
            "insertStrategy": null,
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "headline",
            "mode": "partial",
            "watchedFields": ["title", "headlineElement", "showProductTitle", "linkingType", "productId"],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/headline.html.twig",
            "domSelector": "[data-elysium-slide-headline]",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "afterbegin"
        },
        {
            "name": "description",
            "mode": "partial",
            "watchedFields": ["description", "showProductDescription", "linkingType", "productId"],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/description.html.twig",
            "domSelector": "[data-elysium-slide-description]",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "beforeend"
        },
        {
            "name": "button",
            "mode": "partial",
            "watchedFields": ["buttonLabel", "url", "buttonAppearance", "buttonSize", "linkingOverlay", "linkingOpenExternal", "linkingType"],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/button.html.twig",
            "domSelector": ".blur-elysium-slide-actions",
            "insertStrategy": "replace",
            "fallbackContainer": ".blur-elysium-slide-content",
            "fallbackPosition": "beforeend"
        },
        {
            "name": "cover",
            "mode": "partial",
            "watchedFields": ["contentSettings", "slideCover", "slideCoverMobile", "slideCoverTablet", "slideCoverVideo", "showProductFocusImage", "linkingType"],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/cover.html.twig",
            "domSelector": ".blur-elysium-slide-cover-picture, .blur-elysium-slide-cover-video",
            "insertStrategy": "replace-inner",
            "fallbackContainer": null,
            "fallbackPosition": null
        },
        {
            "name": "focus-image",
            "mode": "partial",
            "watchedFields": ["presentationMedia", "showProductFocusImage", "productId", "linkingType"],
            "template": "@Storefront/storefront/component/blur-elysium-slide/includes/image.html.twig",
            "domSelector": "[data-elysium-slide-focus-image]",
            "insertStrategy": "replace",
            "fallbackContainer": "[data-elysium-slide-container]",
            "fallbackPosition": "beforeend"
        }
    ]
};
