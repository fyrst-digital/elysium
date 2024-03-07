export default {
    slide: {
        /** @type object<slideLinking> | null */
        linking: {
            /** @type 'custom' | 'product' */
            type: 'custom',
            /** @type string | null */
            productId: null,
            /** @type 'internal' | 'external' */
            target: 'internal',
            /** @type 'primary' | 'secondary' */
            buttonAppearance: 'primary',
            /** @type boolean */
            urlOverlay: false
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
            gradientType: '',
            gradientDeg: 45
        }
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
                imageFullWidth: false,
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
                justifyContent: 'center',
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
                fontSize: null
            },
            description: {
                /** @type int | null */
                fontSize: null
            },
            button: {
                /** @type 'default' | 'sm' | 'lg' */
                size: 'default'
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
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type boolean */
                columnWrap: false,
                /** @type 'default' | 'reverse' */
                order: 'default'
            },
            slide: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center',
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
                fontSize: null
            },
            description: {
                /** @type int | null */
                fontSize: null
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
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type boolean */
                columnWrap: false,
                /** @type 'default' | 'reverse' */
                order: 'default'
            },
            slide: {
                /** @type int */
                paddingX: 15,
                /** @type int */
                paddingY: 15,
                /** @type 'stretch' | 'flex-start' | 'center' | 'flex-end' */
                alignItems: 'center',
                /** @type 'flex-start' | 'center' | 'flex-end' */
                justifyContent: 'center',
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
                fontSize: null
            },
            description: {
                /** @type int | null */
                fontSize: null
            }
        }
    },
    slideCover: {
        /** @type 'cover' | 'contain' | 'auto' */
        bgSize: 'cover',
        /** @type 'left' | 'center' | 'right' */
        bgPosX: 'center',
        /** @type 'top' | 'center' | 'bottom' */
        bgPosY: 'center'
    },
    slideCoverPortrait: {
        /** @type 'cover' | 'contain' | 'auto' */
        bgSize: 'cover',
        /** @type 'left' | 'center' | 'right' */
        bgPosX: 'center',
        /** @type 'top' | 'center' | 'bottom' */
        bgPosY: 'center'
    }
}
