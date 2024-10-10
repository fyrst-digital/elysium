Shopware.Component.register(
    'cms-el-blur-elysium-banner', 
    () => import('blurElysium/component/cms/elements/blur-elysium-banner/component')
)

Shopware.Component.register(
    'cms-el-blur-elysium-banner-config', 
    () => import('blurElysium/component/cms/elements/blur-elysium-banner/config')
)

Shopware.Component.register(
    'cms-el-blur-elysium-banner-preview', 
    () => import('blurElysium/component/cms/elements/blur-elysium-banner/preview')
)

Shopware.Service('cmsService').registerCmsElement({
    name: 'blur-elysium-banner',
    label: 'blurElysiumBanner.label',
    component: 'cms-el-blur-elysium-banner',
    configComponent: 'cms-el-blur-elysium-banner-config',
    previewComponent: 'cms-el-blur-elysium-banner-preview',
    defaultConfig: {
        elysiumSlide: { /** @type object */
            source: 'static', /** @type 'static' | 'source' */
            value: '' /** @type string */
        },
        lazyLoading: { 
            source: 'static',
            value: true
        },
        /** @type object */
        viewports: {
            /** @type 'static' | 'source' */
            source: 'static',
            /** @type object<StandardDevices> */
            value: {
                /** @type object<DeviceSettings> */
                mobile: {
                    aspectRatio: {
                        width: 1,
                        height: 1,
                        auto: false
                    },
                    /** @type null | number */
                    maxHeight: null
                },
                tablet: {
                    aspectRatio: {
                        width: 4,
                        height: 3,
                        auto: false
                    },
                    /** @type null | number */
                    maxHeight: null
                },
                desktop: {
                    aspectRatio: {
                        width: 16,
                        height: 9,
                        auto: false
                    },
                    /** @type null | number */
                    maxHeight: null
                }
            }
        }
    }
})
