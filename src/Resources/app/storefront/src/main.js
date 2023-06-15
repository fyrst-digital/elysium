// import elysium slider plugin
//
import BlurElysiumSlider from './js/blur-elysium-slider.js';

// register plugin
// 
window.PluginManager.register('BlurElysiumSliderPlugin', BlurElysiumSlider, '[data-blur-elysium-slider]', {
    "splideSelector": "[data-blur-elysium-slider-container]"
});
