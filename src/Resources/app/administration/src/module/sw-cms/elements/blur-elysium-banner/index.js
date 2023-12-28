import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blur-elysium-banner',
    label: 'blurElysiumBanner.cmsElement.label',
    component: 'blur-cms-el-elysium-banner',
    configComponent: 'blur-cms-el-config-elysium-banner',
    previewComponent: 'blur-cms-el-preview-elysium-banner',
    defaultConfig: {
        elysiumSlide: { /** @type object */
            source: 'static', /** @type 'static' | 'source' */
            value: '' /** @type string */
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
                        auto: true
                    }
                },
                tablet: {
                    aspectRatio: {
                        width: 4,
                        height: 3,
                        auto: true
                    }
                },
                desktop: {
                    aspectRatio: {
                        width: 16,
                        height: 9,
                        auto: true
                    }
                }
            } 
        }
    }
});