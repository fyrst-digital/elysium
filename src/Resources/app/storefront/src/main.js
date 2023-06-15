// get the Plugin Manager Object
//
import PluginManager from 'src/plugin-system/plugin.manager';

// import elysium slider plugin
//
import BlurElysiumSlider from './js/blur-elysium-slider.js';

/**
 * @todo find a way to include the styles in css build file
 */
import "@splide/dist/css/splide-core.min.css";

// register plugin
// 
PluginManager.register('BlurElysiumSliderPlugin', BlurElysiumSlider, '[data-blur-elysium-slider]', {
    "splideSelector": "[data-blur-elysium-slider-container]"
});
