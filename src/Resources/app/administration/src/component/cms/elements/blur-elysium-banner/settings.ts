import { BannerSettings, ViewportSettings } from 'blurElysium/types/banner';

const { Utils } = Shopware

const viewportSettings: ViewportSettings = {
    aspectRatio: {
        width: 1,
        height: 1,
        auto: false
    },
    maxHeight: null
}

function defineViewportConfig(overrides?: Partial<ViewportSettings>): ViewportSettings {
    return Utils.object.deepMergeObject(structuredClone(viewportSettings), overrides)
}

export default <BannerSettings>{
    elysiumSlide: { 
        source: 'static', 
        value: ''
    },
    lazyLoading: { 
        source: 'static',
        value: true
    },
    viewports: {
        source: 'static',
        value: {
            mobile: defineViewportConfig({
                aspectRatio: {
                    width: 1,
                    height: 1,
                    auto: false
                },
                maxHeight: null
            }),
            tablet: defineViewportConfig({
                aspectRatio: {
                    width: 4,
                    height: 3,
                    auto: false
                },
                maxHeight: null
            }),
            desktop: defineViewportConfig({
                aspectRatio: {
                    width: 16,
                    height: 9,
                    auto: false
                },
                maxHeight: null
            })
        }
    }
}