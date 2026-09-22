import { pauseCoverVideos } from './js/utils/slide-inert'

const { PluginManager } = window

PluginManager.register('ElysiumSliderPlugin', () => import('./js/elysium-slider'), '[data-elysium-slider]')
PluginManager.register('ElysiumSlidePreview', () => import('./js/elysium-slide-preview'), '[data-elysium-slide-preview]')

if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.addEventListener('DOMContentLoaded', () => {
        pauseCoverVideos(document)
    })
}
