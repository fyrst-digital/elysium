import enGB from 'blurElysium/snippet/en-GB.json'
import deDE from 'blurElysium/snippet/de-DE.json'
import slideStore from 'blurElysium/states/slide.states'
import uiStore from 'blurElysium/states/ui.states'
import 'blurElysium/styles/mt-fixes.scss'
import 'blurElysium/styles/components.scss'
import 'blurElysium/mixin/device-utilities.mixin'
import 'blurElysium/mixin/style-utilities.mixin'
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

const { Component, Locale, Application, Store } = Shopware

/**
 * Register pinia stores
 */
Store.register(slideStore)
Store.register(uiStore)

/**
 * Register global snnippets
 */
Locale.extend('en-GB', enGB)
Locale.extend('de-DE', deDE)

/**
 * Component extensions
 */
Component.override('sw-search-bar-item', () => import('blurElysium/extension/sw-search-bar-item'));
Component.override('sw-media-quickinfo-usage', () => import('blurElysium/extension/sw-media-quickinfo-usage'));

/**
 * Register components
*/
/** Common components */
Component.register('blur-icon', () => import('blurElysium/component/icon'))
Component.register('blur-section', () => import('blurElysium/component/utilities/section'))
Component.register('blur-column', () => import('blurElysium/component/utilities/column'))
Component.register('blur-card-title', () => import('blurElysium/component/utilities/card-title'))
Component.register('blur-device-switch', () => import('blurElysium/component/form/device-switch'))
/** Form inputs */
Component.register('blur-text-input', () => import('blurElysium/component/form/text-input'))
Component.register('blur-number-input', () => import('blurElysium/component/form/number-input'))
Component.register('blur-select-input', () => import('blurElysium/component/form/select-input'))
Component.register('blur-colorpicker', () => import('blurElysium/component/form/colorpicker'))
/**
 * @deprecated replace `blur-device-number-input` with `blur-number-input`
 * Set prop `showDevice` to `true` to show the device switch
 */
Component.register('blur-device-number-input', () => import('blurElysium/component/form/device-number-input'))
Component.register('blur-device-select-input', () => import('blurElysium/component/form/device-select-input'))
/** Elysium components */
Component.register('blur-elysium-block-two-col-config', () => import('blurElysium/component/utilities/block-two-col-config'))
Component.register('blur-elysium-icon', () => import('blurElysium/component/utilities/icon'))
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
Component.register('blur-elysium-slides-form-slide', () => import('blurElysium/component/slides/form/slide'))
Component.register('blur-elysium-slides-form-container', () => import('blurElysium/component/slides/form/container'))
Component.register('blur-elysium-slides-form-content', () => import('blurElysium/component/slides/form/content'))
Component.register('blur-elysium-slides-form-custom-template-file', () => import('blurElysium/component/slides/form/custom-template-file'))
Component.register('blur-elysium-slides-form-custom-fields', () => import('blurElysium/component/slides/form/custom-fields'))
Component.register('blur-elysium-slide-selection', () => import('blurElysium/component/utilities/slide-selection'))
Component.register('blur-elysium-slide-selection-item', () => import('blurElysium/component/utilities/slide-selection/item'))
Component.register('blur-elysium-cms-slide-skeleton', () => import('blurElysium/component/utilities/cms-slide-skeleton'))
/** register or override cms-section specific components */
Component.override('sw-cms-section', () => import('blurElysium/extension/sw-cms-section'))
Component.override('sw-cms-stage-section-selection', () => import('blurElysium/extension/sw-cms-stage-section-selection'))
Component.register('blur-elysium-cms-section', () => import('blurElysium/component/cms/section'))
Component.register('blur-elysium-cms-section-add-block', () => import('blurElysium/component/cms/section/add-block'))
Component.register('blur-elysium-cms-section-settings', () => import('blurElysium/component/cms/section/settings'))
Component.register('blur-elysium-cms-section-block-settings', () => import('blurElysium/component/cms/section/block-settings'))

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