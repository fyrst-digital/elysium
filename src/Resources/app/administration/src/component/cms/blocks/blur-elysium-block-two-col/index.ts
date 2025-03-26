Shopware.Component.register(
    'sw-cms-block-blur-elysium-block-two-col', 
    () => import('@elysium/component/cms/blocks/blur-elysium-block-two-col/component')
)

Shopware.Component.register(
    'sw-cms-block-blur-elysium-block-two-col-preview', 
    () => import('@elysium/component/cms/blocks/blur-elysium-block-two-col/preview')
)

// eslint-disable-next-line no-undef
Shopware.Service('cmsService').registerCmsBlock({
    name: 'blur-elysium-block-two-col',
    category: 'blur-elysium-blocks',
    label: 'blurElysiumBlock.blockTwoColLabel',
    component: 'sw-cms-block-blur-elysium-block-two-col',
    previewComponent: 'sw-cms-block-blur-elysium-block-two-col-preview',
    defaultConfig: {
        marginBottom: '',
        marginTop: '',
        marginLeft: '',
        marginRight: '',
        customFields: {
            columnStretch: true,
            viewports: {
                mobile: {
                    width: {
                        colOne: 1,
                        colTwo: 1
                    },
                    gridGap: 0,
                    columnWrap: true
                },
                tablet: {
                    width: {
                        colOne: 1,
                        colTwo: 1
                    },
                    gridGap: 0,
                    columnWrap: false
                },
                desktop: {
                    width: {
                        colOne: 1,
                        colTwo: 1
                    },
                    gridGap: 0,
                    columnWrap: false
                }
            }
        }
    },
    slots: {
        left: 'blur-elysium-banner',
        right: 'blur-elysium-banner'
    }
})
