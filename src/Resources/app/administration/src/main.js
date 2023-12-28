/**
 * @todo #74 dynamic import and registraion of components
 * example: https://github.com/shopware/platform/blob/v6.5.0.0/src/Administration/Resources/app/administration/src/module/sw-product/index.js
 */

import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';
import './assets/scss/global.scss'; // import global bc stylings
import './module/blur-elysium-slides'; // import main module
import './app/component/structure/sw-search-bar-item'; // import search bar result override
import './module/sw-cms/extension/sw-cms-sidebar'; // import CMS Element
import './module/sw-cms/elements/blur-elysium-slider'; // import CMS Element
import './module/sw-cms/elements/blur-elysium-banner'; // import CMS Element
import './module/sw-cms/blocks/blur-elysium-slider'; // import CMS block
import './module/sw-cms/blocks/blur-elysium-banner'; // import CMS block
import './module/sw-cms/blocks/blur-elysium-block-two-col'; // import CMS block

/* eslint no-undef: 'off' */
const { Mixin } = Shopware

// give the mixin a name and feed it into the register function as the second argunment
Mixin.register('blur-editable', {
    computed: {
        editable() {
            return this.acl.editor || this.acl.creator || this.acl.deleter
        }
    }
});

Shopware.Component.register('blur-card-loading', () => import('@elysiumSlider/component/blur-card-loading'));
Shopware.Component.register('blur-elysium-device-switch', () => import('@elysiumSlider/component/blur-elysium-device-switch'));
Shopware.Component.register('blur-elysium-slide-modal-delete', () => import('@elysiumSlider/component/blur-elysium-slide-modal-delete'));
Shopware.Component.register('blur-elysium-slider-config-settings', () => import('@elysiumSlider/component/blur-elysium-slider-config-settings'));
Shopware.Component.register('blur-elysium-slider-config-sizing', () => import('@elysiumSlider/component/blur-elysium-slider-config-sizing'));
Shopware.Component.register('blur-elysium-slider-config-navigation', () => import('@elysiumSlider/component/blur-elysium-slider-config-navigation'));
Shopware.Component.register('blur-elysium-slider-config-arrows', () => import('@elysiumSlider/component/blur-elysium-slider-config-arrows'));
Shopware.Component.register('blur-elysium-slide-settings-content-container', () => import('@elysiumSlider/component/blur-elysium-slide-settings-content-container'));
Shopware.Component.register('blur-elysium-slide-settings-display-general', () => import('@elysiumSlider/component/blur-elysium-slide-settings-display-general'));
Shopware.Component.register('blur-elysium-slide-settings-content', () => import('@elysiumSlider/component/blur-elysium-slide-settings-content'));
Shopware.Component.register('blur-elysium-slide-settings-media', () => import('@elysiumSlider/component/blur-elysium-slide-settings-media'));
Shopware.Component.register('blur-elysium-slide-settings-display', () => import('@elysiumSlider/component/blur-elysium-slide-settings-display'));
Shopware.Component.register('blur-elysium-slide-settings-advanced', () => import('@elysiumSlider/component/blur-elysium-slide-settings-advanced'));
Shopware.Component.register('blur-elysium-slide-selection', () => import('@elysiumSlider/component/blur-elysium-slide-selection'));
Shopware.Component.register('blur-elysium-slide-select-item', () => import('@elysiumSlider/component/blur-elysium-slide-select-item'));
Shopware.Component.register('blur-elysium-slide-template-selection', () => import('@elysiumSlider/component/blur-elysium-slide-template-selection'));
Shopware.Component.register('blur-elysium-block-config', () => import('@elysiumSlider/component/blur-elysium-block-config'));

// register text snippets gobally
Shopware.Locale.extend('en-GB', enGB);
Shopware.Locale.extend('de-DE', deDE);

// add blur_elysium_slides entity to custom field set selection in admin view
const CustomFieldDataProviderService = Shopware.Service("customFieldDataProviderService");
CustomFieldDataProviderService.addEntityName("blur_elysium_slides");

console.log(Shopware.Service( 'cmsService' ))