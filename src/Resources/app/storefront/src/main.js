const { PluginManager } = window;

PluginManager.register('ElysiumSliderPlugin', () => import('./js/elysium-slider'), '[data-elysium-slider]');
