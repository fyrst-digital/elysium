import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';
import './assets/scss/global.scss'; // import global bc stylings
// import enGB from './snippet/en-GB.json';
import './component/blur-elysium-slide-selection';
import './component/blur-elysium-slide-select-item';
import './module/blur-elysium-slides'; // import main module
import './app/component/structure/sw-search-bar-item'; // import search bar result override
import './module/sw-cms/elements/blur-elysium-slider'; // import CMS Element
import './module/sw-cms/blocks/text-image/blur-elysium-slider-block'; // import CMS block
// import "./module/override/sw-media-folder-item";

// register text snippets gobally
Shopware.Locale.extend('en-GB', enGB);
Shopware.Locale.extend('de-DE', deDE);

// add blur_elysium_slides entity to custom field set selection in admin view
const CustomFieldDataProviderService = Shopware.Service("customFieldDataProviderService");
CustomFieldDataProviderService.addEntityName("blur_elysium_slides");