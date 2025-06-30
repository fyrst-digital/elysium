import { useComponentRegister } from '@elysium/composables/components'

const { Service } = Shopware;

useComponentRegister([
    { name: 'sw-cms-block-blur-elysium-slider', path: () => import('@elysium/component/cms/blocks/blur-elysium-slider/component') },
    { name: 'sw-cms-block-blur-elysium-slider-preview', path: () => import('@elysium/component/cms/blocks/blur-elysium-slider/preview') },
])

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
        sizingMode: 'boxed',
    },
    slots: {
        main: 'blur-elysium-slider',
    },
})