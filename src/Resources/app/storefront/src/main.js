import BlurElysiumSlider from './js/blur-elysium-slider';

const PluginManager = window.PluginManager;

PluginManager.register(
    'BlurElysiumSliderPlugin',
    BlurElysiumSlider,
    '[data-blur-elysium-slider]',
    {
        splideSelector: '[data-blur-elysium-slider-container]',
    }
);
