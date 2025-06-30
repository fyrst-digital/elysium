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
    { name: 'blur-elysium-iap-subscription-card', path: () => import('@elysium/component/iap/subscription-card') },
    { name: 'blur-elysium-icon', path: () => import('@elysium/component/utilities/icon') },
    { name: 'blur-elysium-settings', path: () => import('@elysium/component/settings') },
    { name: 'blur-elysium-slides-detail-view', path: () => import('@elysium/component/utilities/detail-view') },
    { name: 'blur-elysium-slides-overview', path: () => import('@elysium/component/slides/overview') },
    { name: 'blur-elysium-slides-detail', path: () => import('@elysium/component/slides/detail') },
    { name: 'blur-elysium-slides-section-base', path: () => import('@elysium/component/slides/section/base') },
    { name: 'blur-elysium-slides-section-media', path: () => import('@elysium/component/slides/section/media') },
    { name: 'blur-elysium-slides-section-display', path: () => import('@elysium/component/slides/section/display') },
    { name: 'blur-elysium-slides-section-advanced', path: () => import('@elysium/component/slides/section/advanced') },
    { name: 'blur-elysium-slides-form-general', path: () => import('@elysium/component/slides/form/general') },
    { name: 'blur-elysium-slides-form-linking', path: () => import('@elysium/component/slides/form/linking') },
    { name: 'blur-elysium-slides-form-cover', path: () => import('@elysium/component/slides/form/cover') },
    { name: 'blur-elysium-slides-form-focus-image', path: () => import('@elysium/component/slides/form/focus-image') },
    { name: 'blur-elysium-slides-form-slide', path: () => import('@elysium/component/slides/form/slide') },
    { name: 'blur-elysium-slides-form-container', path: () => import('@elysium/component/slides/form/container') },
    { name: 'blur-elysium-slides-form-content', path: () => import('@elysium/component/slides/form/content') },
    { name: 'blur-elysium-slides-form-custom-template-file', path: () => import('@elysium/component/slides/form/custom-template-file') },
    { name: 'blur-elysium-slides-form-custom-fields', path: () => import('@elysium/component/slides/form/custom-fields') },
    { name: 'blur-elysium-slide-search', path: () => import('@elysium/component/utilities/slide-search') },
    { name: 'blur-elysium-slide-selection', path: () => import('@elysium/component/utilities/slide-selection') },
    { name: 'blur-elysium-slide-selection-item', path: () => import('@elysium/component/utilities/slide-selection/item') },
    { name: 'blur-elysium-cms-slide-skeleton', path: () => import('@elysium/component/utilities/cms-slide-skeleton') },
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