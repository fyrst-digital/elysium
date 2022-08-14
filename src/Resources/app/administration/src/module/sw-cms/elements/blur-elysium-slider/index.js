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
        sliderNavigation: {
            source: 'static',
            value: true
        },
        sliderOverlay: {
            source: 'static',
            value: true
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
        },
        imageFlowMode: {
            source: 'static',
            value: false
        },
        aspectRatio: {
            source: 'static',
            value: {
                landscape: {
                    width: 16,
                    height: 6
                },
                portrait: {
                    width: 4,
                    height: 3
                }
            }
        }
    }
});