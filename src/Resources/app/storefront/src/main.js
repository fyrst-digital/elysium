// get the Plugin Manager Object
//
import PluginManager from 'src/plugin-system/plugin.manager';

// init plugins
//
import BlurElysiumSlider from './js/blur-elysium-slider.js';

const uspSliderOptions = {
    enabled: true,
    autoplay: false,
    loop: false,
    rewind: true,
    nav: true,
    autoplayButtonOutput: false,
    controls: true,
    responsive: {
        xs: {
            items: 1
        }
    }
}

// register plugins
// 
PluginManager.register('BlurElysiumSlider', BlurElysiumSlider, '.cms-element-elysium-slider', {
    containerSelector: '.cms-element-elysium-slider__slide-container'
});
