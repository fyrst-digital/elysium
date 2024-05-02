const { Component, Service } = Shopware

Component.register(
    'sw-cms-block-blur-elysium-slider', 
    () => import('blurElysium/component/cms/blocks/blur-elysium-slider/component')
)

Component.register(
    'sw-cms-block-blur-elysium-slider-preview', 
    () => import('blurElysium/component/cms/blocks/blur-elysium-slider/preview')
)

// eslint-disable-next-line no-undef
Service('cmsService').registerCmsBlock({
    name: 'blur-elysium-slider',
    category: 'blur-elysium-blocks',
    label: 'blurElysiumSlider.label',
    component: 'sw-cms-block-blur-elysium-slider',
    previewComponent: 'sw-cms-block-blur-elysium-slider-preview',
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
