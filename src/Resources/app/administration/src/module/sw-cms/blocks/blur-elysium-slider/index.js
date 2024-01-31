import './component'
import './preview'

// eslint-disable-next-line no-undef
Shopware.Service('cmsService').registerCmsBlock({
    name: 'blur-elysium-slider-block',
    category: 'blur-elysium-slider-banner',
    label: 'Elysium Slider',
    component: 'sw-cms-block-blur-elysium-slider-block',
    previewComponent: 'sw-cms-preview-blur-elysium-slider-block',
    defaultConfig: {
        marginBottom: '',
        marginTop: '',
        marginLeft: '',
        marginRight: '',
        sizingMode: 'boxed'
    },
    slots: {
        main: 'blur-elysium-slider'
    }
})
