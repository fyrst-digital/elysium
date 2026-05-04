const { PluginManager } = window;

PluginManager.register('ElysiumSliderPlugin', () => import('./js/elysium-slider'), '[data-elysium-slider]');
PluginManager.register('ElysiumSlidePreviewPlugin', () => import('./js/elysium-slide-preview'), '[data-elysium-slide-preview]');
