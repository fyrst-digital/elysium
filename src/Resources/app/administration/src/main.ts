import enGB from '@elysium/snippet/en-GB.json';
import deDE from '@elysium/snippet/de-DE.json';
import slideStore from '@elysium/states/slide.states';
import uiStore from '@elysium/states/ui.states';
import cmsStore from '@elysium/states/cms.states';
import { useComponentRegister, useComponentOverride } from '@elysium/composables/components'
import '@elysium/styles/mt-fixes.scss';
import '@elysium/styles/components.scss';
import '@elysium/mixin/device-utilities.mixin';
import '@elysium/mixin/style-utilities.mixin';
import '@elysium/module/blur-elysium-slides';
// cms elements
import '@elysium/component/cms/elements/blur-elysium-slider';
import '@elysium/component/cms/elements/blur-elysium-banner';
// cms blocks
import '@elysium/component/cms/blocks/blur-elysium-slider';
import '@elysium/component/cms/blocks/blur-elysium-banner';

const { Locale, Application, Store, Service } = Shopware;

/**
 * Register pinia stores
 */
Store.register(slideStore);
Store.register(uiStore);
Store.register(cmsStore);

/**
 * Register global snippets
 */
Locale.extend('en-GB', enGB);
Locale.extend('de-DE', deDE);

/**
 * Add search tag
 */
Application.addServiceProviderDecorator(
    'searchTypeService',
    (searchTypeService) => {
        searchTypeService.upsertType('blur_elysium_slides', {
            entityName: 'blur_elysium_slides',
            placeholderSnippet: 'blurElysium.general.placeholderSearchBar',
            listingRoute: 'blur.elysium.slides.overview',
            hideOnGlobalSearchBar: false,
        });

        return searchTypeService;
    }
);

/**
 * add blur_elysium_slides entity to custom field set selection in admin view
 * eslint-disable-next-line no-undef
 */
const CustomFieldDataProviderService = Service(
    'customFieldDataProviderService'
);
CustomFieldDataProviderService.addEntityName('blur_elysium_slides');

/**
 * Register components
 * @todo change the name of elysium related components from `blur-elysium-*` to `elysium-*`

 * @todo change the name of general ui related components from `blur-*` to `sh-*`
 */
useComponentRegister([
    { name: 'blur-icon', path: () => import('@elysium/component/icon') },
    { name: 'blur-section', path: () => import('@elysium/component/utilities/section') },
    { name: 'blur-column', path: () => import('@elysium/component/utilities/column') },
    { name: 'blur-card-title', path: () => import('@elysium/component/utilities/card-title') },
    { name: 'blur-device-switch', path: () => import('@elysium/component/form/device-switch') },
    { name: 'blur-text-input', path: () => import('@elysium/component/form/text-input') },
    { name: 'blur-number-input', path: () => import('@elysium/component/form/number-input') },
    { name: 'blur-select-input', path: () => import('@elysium/component/form/select-input') },
    { name: 'blur-colorpicker', path: () => import('@elysium/component/form/colorpicker') },
    { name: 'elysium-iap-subscription-card', path: () => import('@elysium/component/iap/subscription-card') },
    { name: 'elysium-icon', path: () => import('@elysium/component/utilities/icon') },
    { name: 'elysium-settings', path: () => import('@elysium/component/settings') },
    { name: 'elysium-slides-overview', path: () => import('@elysium/component/slides/overview') },
    { name: 'elysium-slides-detail', path: () => import('@elysium/component/slides/detail') },
    { name: 'elysium-slides-section-base', path: () => import('@elysium/component/slides/section/base') },
    { name: 'elysium-slides-section-media', path: () => import('@elysium/component/slides/section/media') },
    { name: 'elysium-slides-section-display', path: () => import('@elysium/component/slides/section/display') },
    { name: 'elysium-slides-section-advanced', path: () => import('@elysium/component/slides/section/advanced') },
    { name: 'elysium-slides-form-general', path: () => import('@elysium/component/slides/form/general') },
    { name: 'elysium-slides-form-linking', path: () => import('@elysium/component/slides/form/linking') },
    { name: 'elysium-slides-form-cover', path: () => import('@elysium/component/slides/form/cover') },
    { name: 'elysium-slides-form-focus-image', path: () => import('@elysium/component/slides/form/focus-image') },
    { name: 'elysium-slides-form-slide', path: () => import('@elysium/component/slides/form/slide') },
    { name: 'elysium-slides-form-container', path: () => import('@elysium/component/slides/form/container') },
    { name: 'elysium-slides-form-content', path: () => import('@elysium/component/slides/form/content') },
    { name: 'elysium-slides-form-custom-template-file', path: () => import('@elysium/component/slides/form/custom-template-file') },
    { name: 'elysium-slides-form-custom-fields', path: () => import('@elysium/component/slides/form/custom-fields') },
    { name: 'elysium-slide-skeleton', path: () => import('@elysium/component/slide/skeleton') },
    { name: 'elysium-slide-preview-focus-image', path: () => import('@elysium/component/slide/preview/focus-image') },
    { name: 'elysium-slide-preview-cover', path: () => import('@elysium/component/slide/preview/cover') },
    { name: 'elysium-slide-preview-content', path: () => import('@elysium/component/slide/preview/content') },
    { name: 'elysium-slide-preview-container', path: () => import('@elysium/component/slide/preview/container') },
    { name: 'elysium-slide-preview', path: () => import('@elysium/component/slide/preview') },
    { name: 'elysium-slide-search', path: () => import('@elysium/component/slide/search') },
    { name: 'elysium-slide-selection', path: () => import('@elysium/component/slide/selection') },
    { name: 'elysium-slide-selection-item', path: () => import('@elysium/component/slide/selection/item') },
    { name: 'blur-elysium-cms-section', path: () => import('@elysium/component/cms/section') },
    { name: 'blur-elysium-cms-section-add-block', path: () => import('@elysium/component/cms/section/add-block') },
    { name: 'blur-elysium-cms-section-settings', path: () => import('@elysium/component/cms/section/settings') },
    { name: 'blur-elysium-cms-section-block-settings', path: () => import('@elysium/component/cms/section/block-settings') },
])

/**
 * Extend components
 */
useComponentOverride([
    { name: 'sw-cms-sidebar', path: () => import('@elysium/extension/sw-cms-sidebar') },
    { name: 'sw-search-bar-item', path: () => import('@elysium/extension/sw-search-bar-item') },
    { name: 'sw-media-quickinfo-usage', path: () => import('@elysium/extension/sw-media-quickinfo-usage') },
    { name: 'sw-cms-section', path: () => import('@elysium/extension/sw-cms-section') },
    { name: 'sw-cms-stage-section-selection', path: () => import('@elysium/extension/sw-cms-stage-section-selection') },
])