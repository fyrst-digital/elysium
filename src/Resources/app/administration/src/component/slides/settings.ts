import { SlideSettings, ViewportConfig } from 'blurElysium/types/slide';

const { Utils } = Shopware

const viewportConfig: ViewportConfig = {
    slide: {
        paddingX: null,
        paddingY: null,
        borderRadius: null,
        alignItems: null,
        justifyContent: null
    },
    container: {
        paddingX: null,
        paddingY: null,
        borderRadius: null,
        maxWidth: null,
        gap: null,
        justifyContent: null,
        alignItems: null,
        columnWrap: true,
        order: 'default'
    },
    content: {
        paddingX: null,
        paddingY: null,
        maxWidth: null,
        textAlign: 'left'
    },
    image: {
        justifyContent: 'center',
        maxWidth: null,
        imageFullWidth: false
    },
    coverMedia: {
        objectPosX: null,
        objectPosY: null,
        objectFit: null        
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
    return Utils.object.deepMergeObject(structuredClone(viewportConfig), overrides)
}

export default <SlideSettings>{
    slide: {
        headline: {
            color: '',
            element: 'div'
        },
        description: {
            color: null
        },
        linking: {
            type: 'custom',
            buttonAppearance: 'primary',
            buttonSize: 'md',
            openExternal: false,
            overlay: false,
            showProductFocusImage: true
        },
        bgColor: '',
        bgGradient: {
            startColor: '',
            endColor: '',
            gradientType: 'linear-gradient',
            gradientDeg: 45
        },
        cssClass: null
    },
    container: {
        bgColor: '',
        bgEffect: {
            blur: '8px'
        }
    },
    viewports: {
        mobile: defineViewportConfig({
            slide: {
                paddingY: 15,
                paddingX: 15,
                borderRadius: 0,
            },
            container: {
                paddingX: 15,
                paddingY: 15,
                gap: 20,
                justifyContent: 'normal',
                alignItems: 'center',
            },
            coverMedia: {
                objectPosX: 'center',
                objectPosY: 'center',
                objectFit: 'cover'
            }
        }),
        tablet: defineViewportConfig({
            container: {
                columnWrap: false,
            },
            headline: {
                fontSize: 32
            },
            description: {
                fontSize: 16
            }
        }),
        desktop: defineViewportConfig({
            container: {
                columnWrap: false,
            },
            headline: {
                fontSize: 40
            },
            description: {
                fontSize: 20
            }
        })
    },
    slideTemplate: 'default',
    customTemplateFile: null
}
