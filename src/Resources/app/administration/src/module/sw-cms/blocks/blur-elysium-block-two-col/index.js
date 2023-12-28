import "./component";
import "./preview";

Shopware.Service( 'cmsService' ).registerCmsBlock({
    name: 'blur-elysium-block-two-col',
    category: 'blur-elysium-slider-banner',
    label: 'blurElysiumBlock.cmsElement.twoCol',
    component: 'sw-cms-block-blur-elysium-block-two-col',
    previewComponent: 'sw-cms-preview-blur-elysium-block-two-col',
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
                    gridGap: '',
                    columnWrap: true
                },
                tablet: {
                    width: {
                        colOne: 1,
                        colTwo: 1
                    },
                    gridGap: '',
                    columnWrap: false
                },
                desktop: {
                    width: {
                        colOne: 1,
                        colTwo: 1
                    },
                    gridGap: '',
                    columnWrap: false
                }
            }
        }
    },
    slots: {
        one: 'blur-elysium-banner',
        two: 'blur-elysium-banner'
    }
});