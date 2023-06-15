import './component';
import './config';
import './preview';

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
            source: "static",
            value: {
                title: ""
            }
        },
        settings: {
            source: "static",
            value: {
                overlay: false,
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
            source: "static",
            value: {
                active: true,
                position: "below_slider",
                align: "center",
                shape: "circle",
                colors: {
                    "default": "",
                    "active": ""
                },
                size: "md",
                navPadding: "md"
            }
        },
        arrows: {
            source: "static",
            value: {
                active: true,
                icon: {
                    "default": "arrow-head",
                    "customPrev": "",
                    "customNext": ""
                },
                colors: {
                    "default": "",
                    "active": ""
                },
                bgColors: {
                    "default": "",
                    "active": ""
                },
                size: "md",
                position: "in_slider"
            }
        },
        sizing: {
            source: 'static',
            value: [
                {
                    viewport: "xs",
                    position: 1,
                    aspectRatio: {
                        width: 4,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "sm",
                    position: 2,
                    aspectRatio: {
                        width: 5,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "md",
                    position: 3,
                    aspectRatio: {
                        width: 5,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "lg",
                    position: 4,
                    aspectRatio: {
                        width: 2,
                        height: 1,
                    },
                    maxHeight: null
                },
                {
                    viewport: "xl",
                    position: 5,
                    aspectRatio: {
                        width: 12,
                        height: 5,
                    },
                    maxHeight: null
                },
                {
                    viewport: "xxl",
                    position: 6,
                    aspectRatio: {
                        width: 16,
                        height: 7,
                    },
                    maxHeight: null
                }
            ]
        },
        aspectRatio: {
            source: 'static',
            value: [
                {
                    viewport: "xs",
                    position: 1,
                    aspectRatio: {
                        width: 4,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "sm",
                    position: 2,
                    aspectRatio: {
                        width: 5,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "md",
                    position: 3,
                    aspectRatio: {
                        width: 5,
                        height: 3,
                    },
                    maxHeight: null
                },
                {
                    viewport: "lg",
                    position: 4,
                    aspectRatio: {
                        width: 2,
                        height: 1,
                    },
                    maxHeight: null
                },
                {
                    viewport: "xl",
                    position: 5,
                    aspectRatio: {
                        width: 12,
                        height: 5,
                    },
                    maxHeight: null
                },
                {
                    viewport: "xxl",
                    position: 6,
                    aspectRatio: {
                        width: 16,
                        height: 7,
                    },
                    maxHeight: null
                }
            ]
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
});