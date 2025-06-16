import enGB from '@elysium/snippet/en-GB.json';
import deDE from '@elysium/snippet/de-DE.json';
import slideStore from '@elysium/states/slide.states';
import uiStore from '@elysium/states/ui.states';
import cmsStore from '@elysium/states/cms.states';
import '@elysium/styles/mt-fixes.scss';
import '@elysium/styles/components.scss';
import '@elysium/mixin/device-utilities.mixin';
import '@elysium/mixin/style-utilities.mixin';
import '@elysium/module/blur-elysium-slides';
// static loaded components
import SlideSelectionItem from '@elysium/component/utilities/slide-selection/item';
// cms elements
import '@elysium/component/cms/elements/blur-elysium-slider';
import '@elysium/component/cms/elements/blur-elysium-banner';
// cms blocks
import '@elysium/component/cms/blocks/blur-elysium-slider';
import '@elysium/component/cms/blocks/blur-elysium-banner';
// extensions
import '@elysium/extension/sw-cms-sidebar';

const { Component, Locale, Application, Store } = Shopware;

/**
 * Register pinia stores
 */
Store.register(slideStore);
Store.register(uiStore);
Store.register(cmsStore);

/**
 * Register global snnippets
 */
Locale.extend('en-GB', enGB);
Locale.extend('de-DE', deDE);

/**
 * Component extensions
 */
Component.override(
    'sw-search-bar-item',
    () => import('@elysium/extension/sw-search-bar-item')
);
Component.override(
    'sw-media-quickinfo-usage',
    () => import('@elysium/extension/sw-media-quickinfo-usage')
);

/**
 * Register components
 */
/** Common components */
Component.register('blur-icon', () => import('@elysium/component/icon'));
Component.register(
    'blur-section',
    () => import('@elysium/component/utilities/section')
);
Component.register(
    'blur-column',
    () => import('@elysium/component/utilities/column')
);
Component.register(
    'blur-card-title',
    () => import('@elysium/component/utilities/card-title')
);
Component.register(
    'blur-device-switch',
    () => import('@elysium/component/form/device-switch')
);
/** Form inputs */
Component.register(
    'blur-text-input',
    () => import('@elysium/component/form/text-input')
);
Component.register(
    'blur-number-input',
    () => import('@elysium/component/form/number-input')
);
Component.register(
    'blur-select-input',
    () => import('@elysium/component/form/select-input')
);
Component.register(
    'blur-colorpicker',
    () => import('@elysium/component/form/colorpicker')
);
/**
 * @deprecated replace `blur-device-number-input` with `blur-number-input`
 * Set prop `showDevice` to `true` to show the device switch
 */
Component.register(
    'blur-device-number-input',
    () => import('@elysium/component/form/device-number-input')
);
Component.register(
    'blur-device-select-input',
    () => import('@elysium/component/form/device-select-input')
);

/** Elysium components */
Component.register(
    'blur-elysium-block-two-col-config',
    () => import('@elysium/component/utilities/block-two-col-config')
);
Component.register(
    'blur-elysium-icon',
    () => import('@elysium/component/utilities/icon')
);
Component.register(
    'blur-elysium-settings',
    () => import('@elysium/component/settings')
);
Component.register(
    'blur-elysium-slides-detail-view',
    () => import('@elysium/component/utilities/detail-view')
);
Component.register(
    'blur-elysium-slides-overview',
    () => import('@elysium/component/slides/overview')
);
Component.register(
    'blur-elysium-slides-detail',
    () => import('@elysium/component/slides/detail')
);
Component.register(
    'blur-elysium-slides-section-base',
    () => import('@elysium/component/slides/section/base')
);
Component.register(
    'blur-elysium-slides-section-media',
    () => import('@elysium/component/slides/section/media')
);
Component.register(
    'blur-elysium-slides-section-display',
    () => import('@elysium/component/slides/section/display')
);
Component.register(
    'blur-elysium-slides-section-advanced',
    () => import('@elysium/component/slides/section/advanced')
);
Component.register(
    'blur-elysium-slides-form-general',
    () => import('@elysium/component/slides/form/general')
);
Component.register(
    'blur-elysium-slides-form-linking',
    () => import('@elysium/component/slides/form/linking')
);
Component.register(
    'blur-elysium-slides-form-cover',
    () => import('@elysium/component/slides/form/cover')
);
Component.register(
    'blur-elysium-slides-form-focus-image',
    () => import('@elysium/component/slides/form/focus-image')
);
Component.register(
    'blur-elysium-slides-form-slide',
    () => import('@elysium/component/slides/form/slide')
);
Component.register(
    'blur-elysium-slides-form-container',
    () => import('@elysium/component/slides/form/container')
);
Component.register(
    'blur-elysium-slides-form-content',
    () => import('@elysium/component/slides/form/content')
);
Component.register(
    'blur-elysium-slides-form-custom-template-file',
    () => import('@elysium/component/slides/form/custom-template-file')
);
Component.register(
    'blur-elysium-slides-form-custom-fields',
    () => import('@elysium/component/slides/form/custom-fields')
);
Component.register(
    'blur-elysium-slide-search',
    () => import('@elysium/component/utilities/slide-search')
);
Component.register(
    'blur-elysium-slide-selection',
    () => import('@elysium/component/utilities/slide-selection')
);
Component.register('blur-elysium-slide-selection-item', SlideSelectionItem);
Component.register(
    'blur-elysium-cms-slide-skeleton',
    () => import('@elysium/component/utilities/cms-slide-skeleton')
);
/** register or override cms-section specific components */
Component.override(
    'sw-cms-section',
    () => import('@elysium/extension/sw-cms-section')
);
Component.override(
    'sw-cms-stage-section-selection',
    () => import('@elysium/extension/sw-cms-stage-section-selection')
);
Component.register(
    'blur-elysium-cms-section',
    () => import('@elysium/component/cms/section')
);
Component.register(
    'blur-elysium-cms-section-add-block',
    () => import('@elysium/component/cms/section/add-block')
);
Component.register(
    'blur-elysium-cms-section-settings',
    () => import('@elysium/component/cms/section/settings')
);
Component.register(
    'blur-elysium-cms-section-block-settings',
    () => import('@elysium/component/cms/section/block-settings')
);

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
const CustomFieldDataProviderService = Shopware.Service(
    'customFieldDataProviderService'
);
CustomFieldDataProviderService.addEntityName('blur_elysium_slides');
