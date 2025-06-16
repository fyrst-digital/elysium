Shopware.Component.register(
    'sw-cms-block-blur-elysium-banner',
    () => import('@elysium/component/cms/blocks/blur-elysium-banner/component')
);

Shopware.Component.register(
    'sw-cms-block-blur-elysium-banner-preview',
    () => import('@elysium/component/cms/blocks/blur-elysium-banner/preview')
);

// eslint-disable-next-line no-undef
Shopware.Service('cmsService').registerCmsBlock({
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
