import { SliderSettings, ViewportSettings } from 'blurElysium/types/slider';

const { Utils } = Shopware

const viewportSettings: ViewportSettings = {
    settings: {
        slidesPerPage: null
    },
    navigation: {
        size: null,
        gap: null
    },
    arrows: {
        iconSize: null
    },
    sizing: {
        aspectRatio: {
            width: null,
            height: null,
            auto: null
        },
        maxHeight: null,
        maxHeightScreen: null,
        paddingY: null,
        paddingX: null,
        slidesGap: null
    }
}

function defineViewportConfig(overrides?: Partial<ViewportSettings>): ViewportSettings {
    return Utils.object.deepMergeObject(structuredClone(viewportSettings), overrides)
}

export default <SliderSettings>{
    elysiumSlideCollection: {
        source: 'static',
        value: []
    },
    content: {
        source: 'static',
        value: {
            title: '',
        }
    },
    settings: {
        source: 'static',
        value: {
            overlay: false,
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
            mobile: defineViewportConfig({
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
                    },
                    paddingY: 40,
                    paddingX: 40,
                }
            }),
            tablet: defineViewportConfig({
                navigation: {
                    gap: 20
                },
                arrows: {
                    iconSize: 20
                },
                sizing: {
                    aspectRatio: {
                        width: 4,
                        height: 3,
                    },
                    paddingY: 64,
                    paddingX: 64
                }
            }),
            desktop: defineViewportConfig({
                navigation: {
                    gap: 24
                },
                arrows: {
                    iconSize: 24
                },
                sizing: {
                    aspectRatio: {
                        width: 16,
                        height: 9,
                    },
                    paddingY: 64,
                    paddingX: 80
                }
            })
        }
    }
}