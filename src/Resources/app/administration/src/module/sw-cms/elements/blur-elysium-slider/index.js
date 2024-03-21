import './component'
import './config'
import './preview'

// eslint-disable-next-line no-undef
Shopware.Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.cmsElement.label',
    component: 'blur-cms-el-elysium-slider',
    configComponent: 'blur-cms-el-config-elysium-slider',
    previewComponent: 'blur-cms-el-preview-elysium-slider',
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
                },
                viewports: {
                    /** @type object<DeviceSettings> */
                    mobile: {
                        slidesPerPage: 1
                    },
                    tablet: {
                        slidesPerPage: 1
                    },
                    desktop: {
                        slidesPerPage: 1
                    }
                },
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
                },
                viewports: {
                    /** @type object<DeviceSettings> */
                    mobile: {
                        size: 'sm',
                        gap: 16,
                    },
                    tablet: {
                        size: 'sm',
                        gap: 20,
                    },
                    desktop: {
                        size: 'md',
                        gap: 24,
                    }
                },
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
                position: 'in_slider',
                viewports: {
                    /** @type object<DeviceSettings> */
                    mobile: {
                        size: 16,
                    },
                    tablet: {
                        size: 20,
                    },
                    desktop: {
                        size: 24,
                    }
                }
            }
        },
        sizing: {
            source: 'static',
            value: {
                viewports: {
                    /** @type object<DeviceSettings> */
                    mobile: {
                        aspectRatio: {
                            width: 1,
                            height: 1,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 0,
                        paddingX: 0,
                        slidesGap: 0
                    },
                    tablet: {
                        aspectRatio: {
                            width: 4,
                            height: 3,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 0,
                        paddingX: 0,
                        slidesGap: 0
                    },
                    desktop: {
                        aspectRatio: {
                            width: 16,
                            height: 9,
                            auto: false
                        },
                        /** @type int | 'screen' | null */
                        maxHeight: null,
                        maxHeightScreen: false,
                        paddingY: 0,
                        paddingX: 0,
                        slidesGap: 0
                    }
                },
            }
        },
        sliderNavigation: {
            source: 'static',
            value: true
        },
        sliderOverlay: {
            source: 'static',
            value: false
        },
        slideSpeed: {
            source: 'static',
            value: 300
        },
        sliderAutoplay: {
            source: 'static',
            value: true
        },
        sliderAutoplayTimeout: {
            source: 'static',
            value: 5000
        },
        sliderArrowColor: {
            source: 'static',
            value: null
        },
        sliderDotColor: {
            source: 'static',
            value: null
        },
        sliderDotActiveColor: {
            source: 'static',
            value: null
        }
    }
})
