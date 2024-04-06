export default {
    slide: {
        headline: {
            /** @type string | null */
            color: null,
            /** @type 'div' | 'h1' */
            element: 'div'
        },
        description: {
            /** @type string | null */
            color: null
        },
        /** @type object<slideLinking> | null */
        linking: {
            /** @type 'custom' | 'product' */
            type: 'custom',
            /** @type 'primary' | 'secondary' */
            buttonAppearance: 'primary',
            /** @type boolean */
            openExternal: false,
            /** @type boolean */
            overlay: false
        },
        cover: {
            /** @type boolean */
            mobileFirst: false
        },
        /** @type string | null */
        bgColor: null,
        /** @type object<bgGardient> | null */
        bgGradient: {
            startColor: '',
            endColor: '',
            gradientType: 'linear-gradient',
            gradientDeg: 45
        },
        /** @type string | null */
        cssClass: null
    },
    container: {
        /** @type string | null */
        bgColor: null,
        /** @type object<BoxEffects> | null */
        bgEffect: {
            blur: '8px'
        }
    },
    viewports: {
        mobile: {
            container: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type int */
                borderRadius: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type int */
                gap: 20,
                /** @type 'normal' | 'stretch' | 'flex-start' | 'center' | 'flex-end' | 'space-between' | 'space-around' */
                justifyContent: 'normal',
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type boolean */
                columnWrap: true,
                /** @type 'default' | 'reverse' */
                order: 'default'
            },
            content: {
                /** @type int */
                paddingX: 0,
                /** @type int */
                paddingY: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type 'left' | 'center' | 'right' */
                textAlign: 'left'
            },
            image: {
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center',
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type boolean */
                imageFullWidth: false
            },
            slide: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type int */
                borderRadius: 0,
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center'
            },
            coverImage: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            coverVideo: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            headline: {
                /** @type int | null */
                fontSize: 20
            },
            description: {
                /** @type int | null */
                fontSize: 14
            },
            button: {
                /** @type 'default' | 'sm' | 'lg' */
                size: 'sm'
            }
        },
        tablet: {
            container: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type int */
                borderRadius: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type int */
                gap: 20,
                /** @type 'normal' | 'stretch' | 'flex-start' | 'center' | 'flex-end' | 'space-between' | 'space-around' */
                justifyContent: 'normal',
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type boolean */
                columnWrap: false,
                /** @type 'default' | 'reverse' */
                order: 'default'
            },
            content: {
                /** @type int */
                paddingX: 0,
                /** @type int */
                paddingY: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type 'left' | 'center' | 'right' */
                textAlign: 'left'
            },
            image: {
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center',
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type boolean */
                imageFullWidth: false
            },
            slide: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center'
            },
            coverImage: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            coverVideo: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            headline: {
                /** @type int | null */
                fontSize: 24
            },
            description: {
                /** @type int | null */
                fontSize: 16
            },
            button: {
                /** @type 'default' | 'sm' | 'lg' */
                size: 'default'
            }
        },
        desktop: {
            container: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type int */
                borderRadius: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type int */
                gap: 20,
                /** @type 'normal' | 'stretch' | 'flex-start' | 'center' | 'flex-end' | 'space-between' | 'space-around' */
                justifyContent: 'normal',
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type boolean */
                columnWrap: false,
                /** @type 'default' | 'reverse' */
                order: 'default'
            },
            content: {
                /** @type int */
                paddingX: 0,
                /** @type int */
                paddingY: 0,
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type 'left' | 'center' | 'right' */
                textAlign: 'left'
            },
            image: {
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center',
                /** @type int */
                maxWidth: 0,
                /** @type boolean */
                maxWidthDisabled: true,
                /** @type boolean */
                imageFullWidth: false
            },
            slide: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center'
            },
            coverImage: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            coverVideo: {
                /** @type 'left' | 'center' | 'right' */
                objectPosX: 'center',
                /** @type 'top' | 'center' | 'bottom' */
                objectPosY: 'center',
                /** @type 'cover' | 'contain' | 'auto' */
                objectFit: 'cover'
            },
            headline: {
                /** @type int | null */
                fontSize: 32
            },
            description: {
                /** @type int | null */
                fontSize: 20
            },
            button: {
                /** @type 'default' | 'sm' | 'lg' */
                size: 'lg'
            }
        }
    },
    /** @type string | 'default' | null */
    slideTemplate: 'default',
    /** @type string | null */
    customTemplateFile: null
}
