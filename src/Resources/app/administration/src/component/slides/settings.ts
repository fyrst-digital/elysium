import { SlideSettings, ViewportConfig } from 'blurElysium/types/slide';

const { Utils } = Shopware

const viewportConfig: ViewportConfig = {
    container: {
        paddingX: 15,
        paddingY: 15,
        borderRadius: 0,
        maxWidth: 0,
        maxWidthDisabled: true,
        gap: 20,
        justifyContent: 'normal',
        alignItems: 'center',
        columnWrap: true,
        order: 'default'
    },
    content: {
        paddingX: 0,
        paddingY: 0,
        maxWidth: 0,
        maxWidthDisabled: true,
        textAlign: 'left'
    },
    image: {
        justifyContent: 'center',
        maxWidth: 0,
        maxWidthDisabled: true,
        imageFullWidth: false
    },
    slide: {
        paddingX: 15,
        paddingY: 15,
        borderRadius: 0,
        alignItems: 'center',
        justifyContent: 'center'
    },
    coverImage: {
        objectPosX: 'center',
        objectPosY: 'center',
        objectFit: 'cover'
    },
    coverVideo: {
        objectPosX: 'center',
        objectPosY: 'center',
        objectFit: 'cover'
    },
    headline: {
        fontSize: 20
    },
    description: {
        fontSize: 14
    }
}

function defineViewportConfig(overrides?: Partial<ViewportConfig>): ViewportConfig {
    return structuredClone(Utils.object.deepMergeObject(viewportConfig, overrides))
}

export default <SlideSettings>{
    slide: {
        headline: {
            color: null,
            element: 'div'
        },
        description: {
            color: null
        },
        linking: {
            type: 'custom',
            buttonAppearance: 'primary',
            openExternal: false,
            overlay: false,
            showProductFocusImage: true
        },
        bgColor: null,
        bgGradient: {
            startColor: '',
            endColor: '',
            gradientType: 'linear-gradient',
            gradientDeg: 45
        },
        cssClass: null
    },
    container: {
        bgColor: null,
        bgEffect: {
            blur: '8px'
        }
    },
    viewports: {
        mobile: defineViewportConfig(),
        tablet: defineViewportConfig({
            headline: {
                fontSize: 32
            }
        }),
        desktop: defineViewportConfig({
            headline: {
                fontSize: 40
            }
        })
    },
    slideTemplate: 'default',
    customTemplateFile: null
}
