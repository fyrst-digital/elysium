/**
 * @todo #74 dynamic import and registraion of components
 * example: https://github.com/shopware/platform/blob/v6.5.0.0/src/Administration/Resources/app/administration/src/module/sw-product/index.js
 */

import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';
import './assets/scss/global.scss'; // import global bc stylings
import './module/blur-elysium-slides'; // import main module
import './app/component/structure/sw-search-bar-item'; // import search bar result override
import './module/sw-cms/elements/blur-elysium-slider'; // import CMS Element
import './module/sw-cms/blocks/text-image/blur-elysium-slider-block'; // import CMS block

Shopware.Component.register('blur-elysium-slider-config-settings', () => import('@elysiumSlider/component/blur-elysium-slider-config-settings'));
Shopware.Component.register('blur-elysium-slider-config-sizing', () => import('@elysiumSlider/component/blur-elysium-slider-config-sizing'));
Shopware.Component.register('blur-elysium-slider-config-navigation', () => import('@elysiumSlider/component/blur-elysium-slider-config-navigation'));
Shopware.Component.register('blur-elysium-slider-config-arrows', () => import('@elysiumSlider/component/blur-elysium-slider-config-arrows'));
Shopware.Component.register('blur-elysium-slide-selection', () => import('@elysiumSlider/component/blur-elysium-slide-selection'));
Shopware.Component.register('blur-elysium-slide-select-item', () => import('@elysiumSlider/component/blur-elysium-slide-select-item'));

// register text snippets gobally
Shopware.Locale.extend('en-GB', enGB);
Shopware.Locale.extend('de-DE', deDE);

// add blur_elysium_slides entity to custom field set selection in admin view
const CustomFieldDataProviderService = Shopware.Service("customFieldDataProviderService");
CustomFieldDataProviderService.addEntityName("blur_elysium_slides");