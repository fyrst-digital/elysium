import defaultBannerSettings from '@elysium/component/cms/elements/blur-elysium-banner/settings'

Shopware.Component.register(
    'cms-el-blur-elysium-banner', 
    () => import('@elysium/component/cms/elements/blur-elysium-banner/component')
)

Shopware.Component.register(
    'cms-el-blur-elysium-banner-config', 
    () => import('@elysium/component/cms/elements/blur-elysium-banner/config')
)

Shopware.Component.register(
    'cms-el-blur-elysium-banner-preview', 
    () => import('@elysium/component/cms/elements/blur-elysium-banner/preview')
)

Shopware.Service('cmsService').registerCmsElement({
    name: 'blur-elysium-banner',
    label: 'blurElysiumBanner.label',
    component: 'cms-el-blur-elysium-banner',
    configComponent: 'cms-el-blur-elysium-banner-config',
    previewComponent: 'cms-el-blur-elysium-banner-preview',
    defaultConfig: defaultBannerSettings
})
