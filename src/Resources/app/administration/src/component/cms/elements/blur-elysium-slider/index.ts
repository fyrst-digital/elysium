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
    defaultConfig: {
        elysiumSlideCollection: {
            source: 'static',
            value: []
        },
        content: {
            source: 'static',
            value: {
                title: ''
            }
        },
        settings: {
            source: 'static',
            value: {
                overlay: false,
                /** @type 'content' | 'full' */
                containerWidth: 'content',
                rewind: true,
                speed: 300,
                pauseOnHover: true,
                autoplay: {
                    active: true,
                    interval: 5000,
                    pauseOnHover: true
                }
            }
        },
        navigation: {
            source: 'static',
            value: {
                active: true,
                position: 'below_slider',
                align: 'center',
                /** @type 'circle' | 'bar' | 'ring' */
                shape: 'circle',
                colors: {
                    default: '',
                    active: ''
                }
            }
        },
        arrows: {
            source: 'static',
            value: {
                active: true,
                icon: {
                    default: 'arrow-head',
                    customPrev: '',
                    customNext: ''
                },
                colors: {
                    default: '',
                    active: ''
                },
                bgColors: {
                    default: '',
                    active: ''
                },
                position: 'in_slider'
            }
        },
        viewports: {
            source: 'static',
            value: {
                mobile: {
                    settings: {
                        slidesPerPage: 1
                    },
                    navigation: {
                        size: 'sm',
                        gap: 16
                    },
                    arrows: {
                        iconSize: 16
                    },
                    sizing: {
                        aspectRatio: {
                            width: 1,
                            height: 1,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 40,
                        paddingX: 40,
                        slidesGap: 0
                    }
                },
                tablet: {
                    settings: {
                        slidesPerPage: 1
                    },
                    navigation: {
                        size: 'sm',
                        gap: 20
                    },
                    arrows: {
                        iconSize: 20
                    },
                    sizing: {
                        aspectRatio: {
                            width: 4,
                            height: 3,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 64,
                        paddingX: 64,
                        slidesGap: 0
                    }
                },
                desktop: {
                    settings: {
                        slidesPerPage: 1
                    },
                    navigation: {
                        size: 'md',
                        gap: 24
                    },
                    arrows: {
                        iconSize: 24
                    },
                    sizing: {
                        aspectRatio: {
                            width: 16,
                            height: 9,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 64,
                        paddingX: 80,
                        slidesGap: 0
                    }
                }
            }
        }
    }
})
