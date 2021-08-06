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
        }
    }
});