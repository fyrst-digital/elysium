import { useComponentRegister } from '@elysium/composables/components'
import defaultSliderSettings from '@elysium/component/cms/elements/blur-elysium-slider/settings'

const { Service } = Shopware;

useComponentRegister([
    { name: 'cms-el-blur-elysium-slider', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/component') },
    { name: 'blur-elysium-slider-config-settings', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/config/settings') },
    { name: 'cms-el-blur-elysium-slider-config', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/config') },
    { name: 'cms-el-blur-elysium-slider-preview', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/preview') },
    { name: 'blur-elysium-slider-config-sizing', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/config/sizing') },
    { name: 'blur-elysium-slider-config-navigation', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/config/navigation') },
    { name: 'blur-elysium-slider-config-arrows', path: () => import('@elysium/component/cms/elements/blur-elysium-slider/config/arrows') },
])

Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.label',
    component: 'cms-el-blur-elysium-slider',
    configComponent: 'cms-el-blur-elysium-slider-config',
    previewComponent: 'cms-el-blur-elysium-slider-preview',
    defaultConfig: defaultSliderSettings,
});
