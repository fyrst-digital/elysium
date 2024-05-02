/**
 * Register slider plugin dynamiclly
 */
window.PluginManager.register('BlurElysiumSliderPlugin', () => import('./js/blur-elysium-slider.js'), '[data-blur-elysium-slider]', {
    splideSelector: '[data-blur-elysium-slider-container]'
})