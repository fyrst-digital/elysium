import enGB from 'blurElysium/snippet/en-GB.json'
import deDE from 'blurElysium/snippet/de-DE.json'
import slideStore from 'blurElysium/component/slides/store'
import 'blurElysium/mixin/media.mixin'
import 'blurElysium/module/blur-elysium-slides'
// cms elements
import 'blurElysium/component/cms/elements/blur-elysium-slider'
import 'blurElysium/component/cms/elements/blur-elysium-banner'
// cms blocks
import 'blurElysium/component/cms/blocks/blur-elysium-slider'
import 'blurElysium/component/cms/blocks/blur-elysium-banner'
import 'blurElysium/component/cms/blocks/blur-elysium-block-two-col'
// extensions
import 'blurElysium/extension/sw-cms-sidebar'

const { Component, Locale, State, Application } = Shopware

State.registerModule('blurElysiumSlide', slideStore);

/**
 * Global snippet registration
 */
Locale.extend('en-GB', enGB)
Locale.extend('de-DE', deDE)

/**
 * Component extensions
 */
Component.override('sw-search-bar-item', () => import('blurElysium/extension/sw-search-bar-item'));

/**
 * Register components
*/
Component.register('blur-icon', () => import('blurElysium/component/icon'))
Component.register('blur-card-title', () => import('blurElysium/component/utilities/card-title'))
Component.register('blur-card-section', () => import('blurElysium/component/utilities/card-section'))
Component.register('blur-device-switch', () => import('blurElysium/component/utilities/device-switch'))
Component.register('blur-device-number-field', () => import('blurElysium/component/utilities/device-number-field'))
Component.register('blur-device-select-field', () => import('blurElysium/component/utilities/device-select-field'))
Component.register('blur-elysium-block-two-col-config', () => import('blurElysium/component/utilities/block-two-col-config'))
Component.register('blur-elysium-icon', () => import('blurElysium/component/utilities/icon'))
Component.register('blur-elysium-media-upload', () => import('blurElysium/component/media-upload'))
Component.register('blur-elysium-settings', () => import('blurElysium/component/settings'))
Component.register('blur-elysium-slides-detail-view', () => import('blurElysium/component/utilities/detail-view'))
Component.register('blur-elysium-slides-overview', () => import('blurElysium/component/slides/overview'))
Component.register('blur-elysium-slides-detail', () => import('blurElysium/component/slides/detail'))
Component.register('blur-elysium-slides-section-base', () => import('blurElysium/component/slides/section/base'))
Component.register('blur-elysium-slides-section-media', () => import('blurElysium/component/slides/section/media'))
Component.register('blur-elysium-slides-section-display', () => import('blurElysium/component/slides/section/display'))
Component.register('blur-elysium-slides-section-advanced', () => import('blurElysium/component/slides/section/advanced'))
Component.register('blur-elysium-slides-form-general', () => import('blurElysium/component/slides/form/general'))
Component.register('blur-elysium-slides-form-linking', () => import('blurElysium/component/slides/form/linking'))
Component.register('blur-elysium-slides-form-cover', () => import('blurElysium/component/slides/form/cover'))
Component.register('blur-elysium-slides-form-focus-image', () => import('blurElysium/component/slides/form/focus-image'))
Component.register('blur-elysium-slides-form-display-slide', () => import('blurElysium/component/slides/form/display-slide'))
Component.register('blur-elysium-slides-form-display-container', () => import('blurElysium/component/slides/form/display-container'))
Component.register('blur-elysium-slides-form-display-content', () => import('blurElysium/component/slides/form/display-content'))
Component.register('blur-elysium-slides-form-display-image', () => import('blurElysium/component/slides/form/display-image'))
Component.register('blur-elysium-slides-form-custom-template-file', () => import('blurElysium/component/slides/form/custom-template-file'))
Component.register('blur-elysium-slides-form-custom-fields', () => import('blurElysium/component/slides/form/custom-fields'))
Component.register('blur-elysium-slide-selection', () => import('blurElysium/component/utilities/slide-selection'))
Component.register('blur-elysium-slide-selection-item', () => import('blurElysium/component/utilities/slide-selection/item'))
Component.register('blur-elysium-cms-slide-skeleton', () => import('blurElysium/component/utilities/cms-slide-skeleton'))

/**
 * Add search tag
 */
Application.addServiceProviderDecorator('searchTypeService', searchTypeService => {
    searchTypeService.upsertType('blur_elysium_slides', {
        entityName: 'blur_elysium_slides',
        placeholderSnippet: 'blurElysium.general.placeholderSearchBar',
        listingRoute: 'blur.elysium.slides.overview',
        hideOnGlobalSearchBar: false,
    });

    return searchTypeService;
})

/**
 * add blur_elysium_slides entity to custom field set selection in admin view
 * eslint-disable-next-line no-undef
 */
const CustomFieldDataProviderService = Shopware.Service('customFieldDataProviderService')
CustomFieldDataProviderService.addEntityName('blur_elysium_slides')