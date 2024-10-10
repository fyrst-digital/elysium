import defaultSliderSettings from 'blurElysium/component/cms/elements/blur-elysium-slider/settings'

Shopware.Component.register(
    'cms-el-blur-elysium-slider', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/component')
)

Shopware.Component.register(
    'cms-el-blur-elysium-slider-config', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/config')
)

Shopware.Component.register(
    'cms-el-blur-elysium-slider-preview', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/preview')
)

Shopware.Component.register(
    'blur-elysium-slider-config-settings', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/config/settings')
)
Shopware.Component.register(
    'blur-elysium-slider-config-sizing', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/config/sizing')
)
Shopware.Component.register(
    'blur-elysium-slider-config-navigation', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/config/navigation')
)
Shopware.Component.register(
    'blur-elysium-slider-config-arrows', 
    () => import('blurElysium/component/cms/elements/blur-elysium-slider/config/arrows')
)

Shopware.Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.label',
    component: 'cms-el-blur-elysium-slider',
    configComponent: 'cms-el-blur-elysium-slider-config',
    previewComponent: 'cms-el-blur-elysium-slider-preview',
    defaultConfig: defaultSliderSettings
})
