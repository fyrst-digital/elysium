import "./component";
import "./preview";

Shopware.Service( 'cmsService' ).registerCmsBlock({
    name: 'blur-elysium-banner-block',
    category: 'blur-elysium-slider-banner',
    label: 'blurElysiumBanner.cmsElement.label',
    component: 'sw-cms-block-blur-elysium-banner-block',
    previewComponent: 'sw-cms-preview-blur-elysium-banner-block',
    defaultConfig: {
        marginBottom: '',
        marginTop: '',
        marginLeft: '',
        marginRight: '',
        sizingMode: 'boxed'
    },
    slots: {
        main: 'blur-elysium-banner'
    }
});