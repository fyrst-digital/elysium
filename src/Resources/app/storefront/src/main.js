import BlurElysiumSlider from './js/blur-elysium-slider';

const { PluginManager } = window;

PluginManager.register(
    'BlurElysiumSliderPlugin',
    BlurElysiumSlider,
    '[data-blur-elysium-slider]',
    {
        splideSelector: '[data-blur-elysium-slider-container]',
    }
);

PluginManager.register('ElysiumSliderPlugin', () => import('./js/elysium-slider'), '[data-elysium-slider]');
