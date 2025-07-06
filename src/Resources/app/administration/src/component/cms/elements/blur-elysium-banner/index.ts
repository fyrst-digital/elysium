import { useComponentRegister } from '@elysium/composables/components'
import defaultBannerSettings from '@elysium/component/cms/elements/blur-elysium-banner/settings'

const { Service } = Shopware

useComponentRegister([
    { name: 'cms-el-blur-elysium-banner', path: () => import('@elysium/component/cms/elements/blur-elysium-banner/component') },
    { name: 'cms-el-blur-elysium-banner-config', path: () => import('@elysium/component/cms/elements/blur-elysium-banner/config') },
    { name: 'cms-el-blur-elysium-banner-preview', path: () => import('@elysium/component/cms/elements/blur-elysium-banner/preview') },
])

Service('cmsService').registerCmsElement({
    name: 'blur-elysium-banner',
    label: 'blurElysiumBanner.label',
    component: 'cms-el-blur-elysium-banner',
    configComponent: 'cms-el-blur-elysium-banner-config',
    previewComponent: 'cms-el-blur-elysium-banner-preview',
    defaultConfig: defaultBannerSettings,
})