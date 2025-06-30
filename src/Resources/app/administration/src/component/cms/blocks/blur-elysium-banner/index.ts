import { useComponentRegister } from '@elysium/composables/components'

const { Service } = Shopware;

useComponentRegister([
    { name: 'sw-cms-block-blur-elysium-banner', path: () => import('@elysium/component/cms/blocks/blur-elysium-banner/component') },
    { name: 'sw-cms-block-blur-elysium-banner-preview', path: () => import('@elysium/component/cms/blocks/blur-elysium-banner/preview') },
]);

// eslint-disable-next-line no-undef
Service('cmsService').registerCmsBlock({
    name: 'blur-elysium-banner',
    category: 'blur-elysium-blocks',
    label: 'blurElysiumBanner.label',
    component: 'sw-cms-block-blur-elysium-banner',
    previewComponent: 'sw-cms-block-blur-elysium-banner-preview',
    defaultConfig: {
        marginBottom: '',
        marginTop: '',
        marginLeft: '',
        marginRight: '',
        sizingMode: 'boxed',
    },
    slots: {
        main: 'blur-elysium-banner',
    },
});
